<?php

namespace App\Services;

use App\Models\Paper;
use App\Models\PaperFile;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PaperFileService
{
    public const DISK = 'local';

    /**
     * Upload slots per paper. 'ooxml' is the entry that must exist inside a
     * .docx / .pptx zip so a renamed file of another kind is rejected.
     */
    public const TYPES = [
        PaperFile::TYPE_FULL_PAPER => [
            'label' => 'Full Paper',
            'file_label' => 'FullPaper',
            'extensions' => ['doc', 'docx'],
            'ooxml' => 'word/document.xml',
            'size_setting' => 'full_paper_max_upload_size_mb',
            'default_size_mb' => 10,
        ],
        PaperFile::TYPE_PRESENTATION => [
            'label' => 'Presentation',
            'file_label' => 'Presentation',
            'extensions' => ['ppt', 'pptx'],
            'ooxml' => 'ppt/presentation.xml',
            'size_setting' => 'presentation_max_upload_size_mb',
            'default_size_mb' => 20,
        ],
    ];

    // Header of legacy OLE2 compound files (.doc / .ppt)
    private const OLE_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    public static function settings()
    {
        return Setting::pluck('value', 'key');
    }

    public static function isSubmissionOpen($settings = null): bool
    {
        $settings = $settings ?? self::settings();

        if (($settings['is_paper_file_submission_open'] ?? 'false') != 'true') {
            return false;
        }

        $deadline = trim((string) ($settings['paper_file_submission_deadline'] ?? ''));
        if ($deadline !== '' && Carbon::now()->gt(Carbon::parse($deadline)->endOfDay())) {
            return false;
        }

        return true;
    }

    public static function isReuploadAllowed($settings = null): bool
    {
        $settings = $settings ?? self::settings();

        return ($settings['allow_paper_file_reupload'] ?? 'false') == 'true';
    }

    public static function maxSizeMb(string $type, $settings = null): int
    {
        $settings = $settings ?? self::settings();
        $config = self::TYPES[$type];
        $size = (int) ($settings[$config['size_setting']] ?? 0);

        return $size > 0 ? $size : $config['default_size_mb'];
    }

    /**
     * Upload state of every slot for this paper, as seen by $user.
     * Expects $paper->files to be loaded (or loads it).
     */
    public static function uploadContext(Paper $paper, ?User $user, $settings = null): array
    {
        $settings = $settings ?? self::settings();
        $isOpen = self::isSubmissionOpen($settings);
        $reuploadAllowed = self::isReuploadAllowed($settings);
        $isOwner = $user && $paper->user_id === $user->id;

        $context = [];
        foreach (self::TYPES as $type => $config) {
            $latest = $paper->latestFile($type);

            $reason = null;
            if (!$isOwner) {
                $reason = 'Only the submitting author can upload files.';
            } elseif (!$paper->isApprovedAndPaid()) {
                $reason = 'Upload is available once the abstract is approved and payment is completed.';
            } elseif (!$isOpen) {
                $reason = 'Full paper and presentation submission is currently closed.';
            } elseif ($latest && !$reuploadAllowed) {
                $reason = 'This file has already been submitted. Re-upload is not allowed.';
            }

            $context[$type] = $config + [
                'type' => $type,
                'max_size_mb' => self::maxSizeMb($type, $settings),
                'accept' => '.' . implode(',.', $config['extensions']),
                'latest' => $latest,
                'versions' => $paper->files->where('type', $type)->values(),
                'can_upload' => $reason === null,
                'blocked_reason' => $reason,
            ];
        }

        return $context;
    }

    /**
     * The user's approved & paid papers that still have a file they can upload right now,
     * each with its upload slots (used for the dashboard / profile reminder).
     */
    public static function pendingUploadsFor(?User $user): \Illuminate\Support\Collection
    {
        $settings = self::settings();
        if (!$user || !self::isSubmissionOpen($settings)) {
            return collect();
        }

        return Paper::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('payment_status', '1')
            ->with('files')
            ->orderBy('id')
            ->get()
            ->map(function (Paper $paper) use ($user, $settings) {
                $slots = self::uploadContext($paper, $user, $settings);
                return (object) ['paper' => $paper, 'slots' => $slots];
            })
            // Remind only while something is still missing
            ->filter(fn ($item) => collect($item->slots)->contains(fn ($slot) => $slot['can_upload'] && !$slot['latest']))
            ->values();
    }

    /**
     * Validation rules for one slot. Extension is checked against the client
     * name, and the content is checked against the real file structure.
     */
    public static function rules(string $type, $settings = null): array
    {
        $config = self::TYPES[$type];
        $maxKb = self::maxSizeMb($type, $settings) * 1024;

        return [
            'file' => [
                'required',
                'file',
                'max:' . $maxKb,
                function ($attribute, $value, $fail) use ($config) {
                    if (!$value instanceof UploadedFile) {
                        return;
                    }
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, $config['extensions'], true)) {
                        $fail('Only ' . self::extensionList($config) . ' files are allowed for the ' . $config['label'] . '.');
                        return;
                    }
                    if (!self::contentMatchesExtension($value, $extension, $config)) {
                        $fail('The uploaded file is not a valid ' . self::extensionList($config) . ' document. Please upload the original file.');
                    }
                },
            ],
        ];
    }

    public static function messages(string $type, $settings = null): array
    {
        $config = self::TYPES[$type];
        $maxMb = self::maxSizeMb($type, $settings);

        return [
            'file.required' => 'Please choose a ' . $config['label'] . ' file to upload.',
            'file.uploaded' => 'The file could not be uploaded. It may be larger than the server allows (' . $maxMb . ' MB).',
            'file.max' => 'The ' . $config['label'] . ' must not be larger than ' . $maxMb . ' MB.',
        ];
    }

    /**
     * Stores the file as the next version for this paper/type.
     */
    public static function store(Paper $paper, string $type, UploadedFile $file, User $user): PaperFile
    {
        $config = self::TYPES[$type];
        $extension = strtolower($file->getClientOriginalExtension());

        return DB::transaction(function () use ($paper, $type, $file, $user, $config, $extension) {
            // Lock the paper row so two parallel uploads can't take the same version
            Paper::whereKey($paper->id)->lockForUpdate()->first();

            if (!self::isReuploadAllowed() && PaperFile::where('paper_id', $paper->id)->where('type', $type)->exists()) {
                throw new \RuntimeException('This file has already been submitted. Re-upload is not allowed.');
            }

            $version = (int) PaperFile::where('paper_id', $paper->id)->where('type', $type)->max('version') + 1;
            $submissionId = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($paper->submission_id ?: 'paper-' . $paper->id));
            $fileName = $submissionId . '_' . $config['file_label'] . '_v' . $version . '.' . $extension;
            $directory = 'paper_files/' . $paper->id . '/' . $type;

            $path = $file->storeAs($directory, $fileName, self::DISK);
            if (!$path) {
                throw new \RuntimeException('Failed to store the uploaded file.');
            }

            try {
                return PaperFile::create([
                    'paper_id' => $paper->id,
                    'type' => $type,
                    'version' => $version,
                    'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                    'file_name' => $fileName,
                    'disk' => self::DISK,
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            } catch (\Throwable $e) {
                Storage::disk(self::DISK)->delete($path);
                throw $e;
            }
        });
    }

    // Formats that can be rendered in the browser (docx-preview / pptx-preview); others are download-only
    public const PREVIEW_MIME_TYPES = [
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    public static function previewKind(PaperFile $file): ?string
    {
        $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));

        return isset(self::PREVIEW_MIME_TYPES[$extension]) ? $extension : null;
    }

    public static function isPreviewable(PaperFile $file): bool
    {
        return self::previewKind($file) !== null;
    }

    private static function extensionList(array $config): string
    {
        return implode(' / ', array_map(fn ($ext) => '.' . $ext, $config['extensions']));
    }

    private static function contentMatchesExtension(UploadedFile $file, string $extension, array $config): bool
    {
        $path = $file->getRealPath();
        if (!$path || !is_readable($path)) {
            return false;
        }

        // .docx / .pptx are zip packages with a known main part
        if (str_ends_with($extension, 'x')) {
            $zip = new ZipArchive();
            if ($zip->open($path) !== true) {
                return false;
            }
            $valid = $zip->locateName('[Content_Types].xml') !== false
                && $zip->locateName($config['ooxml']) !== false;
            $zip->close();

            return $valid;
        }

        // .doc / .ppt are OLE2 compound files
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return false;
        }
        $header = fread($handle, 8);
        fclose($handle);

        return $header === self::OLE_SIGNATURE;
    }
}
