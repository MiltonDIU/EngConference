<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\PaperFile;
use App\Services\PaperFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PaperFileController extends Controller
{
    /**
     * Author uploads the Full Paper or Presentation for an approved & paid paper.
     */
    public function store(Request $request, Paper $paper, string $type)
    {
        $user = Auth::user();
        abort_unless(isset(PaperFileService::TYPES[$type]), Response::HTTP_NOT_FOUND);
        abort_if($paper->user_id !== $user->id, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $settings = PaperFileService::settings();
        $paper->load('files');
        $slot = PaperFileService::uploadContext($paper, $user, $settings)[$type];
        $redirect = redirect()->to(route('papers.show', $paper->id) . '#paper-files');

        if (!$slot['can_upload']) {
            return $redirect->with('error', $slot['blocked_reason']);
        }

        $request->validateWithBag(
            $type,
            PaperFileService::rules($type, $settings),
            PaperFileService::messages($type, $settings)
        );

        try {
            $file = PaperFileService::store($paper, $type, $request->file('file'), $user);
        } catch (\RuntimeException $e) {
            return $redirect->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Paper file upload error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'paper_id' => $paper->id,
                'type' => $type,
            ]);
            return $redirect->with('error', 'Error uploading the file. Please try again or contact support.');
        }

        return $redirect->with('message', $slot['label'] . ' uploaded successfully (' . $file->original_name . ').');
    }

    /**
     * Admin-only download of any stored version.
     */
    public function download(Paper $paper, PaperFile $paperFile)
    {
        $disk = $this->authorizeAdminFile($paper, $paperFile);

        return $disk->download($paperFile->path, $paperFile->file_name);
    }

    /**
     * Admin-only raw .docx / .pptx stream for the in-browser preview.
     */
    public function preview(Paper $paper, PaperFile $paperFile)
    {
        $disk = $this->authorizeAdminFile($paper, $paperFile);
        $kind = PaperFileService::previewKind($paperFile);
        abort_unless($kind !== null, Response::HTTP_UNPROCESSABLE_ENTITY, 'Preview is available for .docx and .pptx files only.');

        return $disk->response($paperFile->path, $paperFile->file_name, [
            'Content-Type' => PaperFileService::PREVIEW_MIME_TYPES[$kind],
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeAdminFile(Paper $paper, PaperFile $paperFile)
    {
        $user = Auth::user();
        abort_if($user->roles->contains('id', 3), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($paperFile->paper_id !== $paper->id, Response::HTTP_NOT_FOUND);

        $disk = Storage::disk($paperFile->disk);
        abort_unless($disk->exists($paperFile->path), Response::HTTP_NOT_FOUND, 'File not found on server.');

        return $disk;
    }
}
