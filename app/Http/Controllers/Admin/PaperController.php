<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\PaperAuthor;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Track;
use App\Models\SubTrack;
use App\Models\Profile;
use App\Mail\AbstractSubmitted;
use App\Mail\AbstractAccepted;
use App\Mail\AbstractRejected;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PaperController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();

            if ($user->roles->contains('id', 3)) {
                $query = Paper::where('user_id', $user->id)->with('user', 'track', 'subTrack', 'authors.country');
            } else {
                abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
                $query = Paper::with('user', 'track', 'subTrack', 'authors.country');
            }

            // Apply Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('track_id')) {
                $query->where('track_id', $request->track_id);
            }
            if ($request->filled('payment_status')) {
                $paymentStatus = $request->payment_status == 'paid' ? '1' : ($request->payment_status == 'unpaid' ? '0' : null);
                if ($paymentStatus !== null) {
                    $query->where('payment_status', $paymentStatus);
                }
            }

            return DataTables::of($query)
                ->addColumn('actions', function ($row) {
                    $viewRoute = route('papers.show', $row->id);
                    $editRoute = route('papers.edit', $row->id);

                    // Show Edit button for authors only if paper is pending
                    $editBtn = '';
                    if (Auth::user()->roles->contains('id', 3) && $row->status === 'pending' && $row->user_id === Auth::id()) {
                        $editBtn = ' <a href="'.$editRoute.'" class="btn btn-sm btn-white border text-info" title="Edit Paper">
                                        <i class="fas fa-edit"></i>
                                    </a>';
                    } elseif (Gate::allows('paper_edit')) {
                        // For Admin/Others with mass edit permission
                         $editBtn = ' <a href="'.$editRoute.'" class="btn btn-sm btn-white border text-info" title="Edit Paper">
                                        <i class="fas fa-edit"></i>
                                    </a>';
                    }

                    $payBtn = '';
                    if ($row->status === 'approved' && $row->payment_status != '1' && Auth::user()->roles->contains('id', 3)) {
                        $payBtn = ' <button class="btn btn-sm btn-primary ml-1" onclick="openPaymentModal('.$row->id.')" title="Payment Review">
                                        <i class="fas fa-credit-card mr-1"></i> Pay
                                    </button>';
                    }

                    return '<div class="btn-group shadow-sm">
                                <a href="'.$viewRoute.'" class="btn btn-sm btn-white border text-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                '.$editBtn.'
                                '.$payBtn.'
                            </div>';
                })
                ->editColumn('submission_id', function ($row) {
                    return '<span class="font-weight-bold text-primary">'.$row->submission_id.'</span>';
                })
                ->addColumn('submitted_by', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('authors', function ($row) {
                    $authors = $row->authors->pluck('name')->toArray();
                    if (empty($authors) && $row->user) {
                        $authors[] = $row->user->name;
                    }
                    $authorText = implode(', ', $authors);
                    return '<div class="text-muted small" title="'.$authorText.'">'.$authorText.'</div>';
                })
                ->addColumn('department', function ($row) {
                    $author = $row->authors->where('is_presenting_author', 1)->first() ?? $row->authors->first();
                    return $author->department ?? 'N/A';
                })
                ->addColumn('institution', function ($row) {
                    $author = $row->authors->where('is_presenting_author', 1)->first() ?? $row->authors->first();
                    return $author->institution ?? 'N/A';
                })
                ->addColumn('country', function ($row) {
                    $author = $row->authors->where('is_presenting_author', 1)->first() ?? $row->authors->first();
                    return $author->country->name ?? 'N/A';
                })
                ->editColumn('title', function ($row) {
                    return '<div class="text-dark font-weight-600 text-truncate" style="max-width: 300px;" title="'.$row->title.'">'.$row->title.'</div>';
                })
                ->editColumn('track', function ($row) {
                    $trackName = $row->track->name ?? 'N/A';
                    $subTrackHtml = $row->subTrack ? '<small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.2;"><i class="fas fa-caret-right mr-1"></i> '.$row->subTrack->name.'</small>' : '';
                    return '<span class="badge badge-light border text-dark px-2 py-1 rounded-pill d-block mb-1 text-truncate" style="max-width: 150px;" title="'.$trackName.'">'.$trackName.'</span>' . $subTrackHtml;
                })
                ->editColumn('status', function ($row) {
                    $statusClass = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger'
                    ][$row->status] ?? 'secondary';

                    $badge = '<span class="badge badge-'.$statusClass.' px-3 py-2 text-uppercase shadow-none border-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">'.$row->status.'</span>';

                    if ($row->status === 'approved') {
                        $pStatusClass = $row->payment_status == '1' ? 'success' : 'warning';
                        $pStatusText = $row->payment_status == '1' ? 'PAID' : 'UNPAID';
                        $badge .= '<span class="badge badge-'.$pStatusClass.' d-block mt-1" style="font-size: 0.65rem;">'.$pStatusText.'</span>';
                    }

                    return $badge;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('M d, Y') : '';
                })
                ->rawColumns(['actions', 'submission_id', 'title', 'authors', 'track', 'status'])
                ->make(true);
        }

        $tracks = Track::all();
        return view('admin.papers.index', compact('tracks'));
    }

    public function getPaperPricing(Paper $paper)
    {
        $user = Auth::user();
        if ($user->roles->contains('id', 3) && $paper->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized access to this paper.'], 403);
        }

        if (!$user->profile) {
            Log::warning('Paper pricing failed: Profile not found for User ID ' . $user->id);
            return response()->json(['error' => 'Your profile details are missing. Please complete your profile first.'], 422);
        }

        try {
            $pricing = \App\Services\PricingService::calculatePaperCost($user->profile, $paper);
            $authors = $paper->authors->map(function($author) {
                return [
                    'name' => $author->name,
                    'designation' => $author->designation
                ];
            });

            return response()->json([
                'submission_id' => $paper->submission_id,
                'pricing' => $pricing,
                'authors' => $authors,
                'paper_id' => $paper->id,
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Paper pricing calculation error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'paper_id' => $paper->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to calculate pricing. Details: ' . $e->getMessage()], 500);
        }
    }

    public function show(Paper $paper)
    {
        $user = Auth::user();
        // Access check: Participant can only see their own paper
        if ($user->roles->contains('id', 3)) {

            if ($paper->user_id != $user->id) {
                abort(403);
            }
        } else {

            abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $paper->load('authors', 'user', 'reviewHistory.reviewer');

        return view('admin.papers.show', compact('paper'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is an author
        $profile = Profile::where('user_id', $user->id)->first();


        if (!$profile || !$profile->is_author) {
            return redirect()->route('show-profile')->with('error', 'Only registered authors can submit papers.');
        }


        $settings = Setting::pluck('value', 'key');
        if (($settings['is_abstract_submission_open'] ?? 'true') == 'false') {
            return redirect()->route('show-profile')->with('error', 'Abstract submission is currently closed.');
        }

        $maxSubmissions = (int) ($settings['maximum_abstract_submission'] ?? $settings['maximum_abastract_submission'] ?? 1);
        $userPaperCount = Paper::where('user_id', $user->id)->count();

        if ($userPaperCount >= $maxSubmissions) {
            return redirect()->route('papers.index')->with('error', 'You have reached the maximum allowed limit of ' . $maxSubmissions . ' abstract submissions.');
        }

        $countries = Country::all();
        $tracks = Track::with('subTracks')->get();

        return view('admin.papers.create', compact('countries', 'tracks'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = Profile::where('user_id', $user->id)->first();
        if (!$profile || !$profile->is_author) {
            return redirect()->route('show-profile')->with('error', 'Only registered authors can submit papers.');
        }

        $settings = Setting::pluck('value', 'key');
        if (($settings['is_abstract_submission_open'] ?? 'true') == 'false') {
            return redirect()->route('show-profile')->with('error', 'Abstract submission is currently closed.');
        }

        $maxSubmissions = (int) ($settings['maximum_abstract_submission'] ?? $settings['maximum_abastract_submission'] ?? 1);
        $userPaperCount = Paper::where('user_id', $user->id)->count();

        if ($userPaperCount >= $maxSubmissions) {
            return redirect()->route('papers.index')->with('error', 'You have reached the maximum allowed limit of ' . $maxSubmissions . ' abstract submissions.');
        }

        // PHP Tag Check Regex
        $noPhpTags = 'regex:/^((?!(<\?php|<\?|\?>)).)*$/is';

        $request->validate([
            'paper_title' => ['required', 'string', 'max:255', $noPhpTags],
            'abstract_text' => ['required', 'string', $noPhpTags, function ($attribute, $value, $fail) {
                $wordCount = !empty(trim($value)) ? preg_match_all('/\s+/', trim($value)) + 1 : 0;
                if ($wordCount > 300) {
                    $fail('The abstract must not exceed 300 words. (Current count: ' . $wordCount . ')');
                }
            }],
            'keywords' => ['required', 'string', 'max:255', $noPhpTags, function ($attribute, $value, $fail) {
                $keywords = array_filter(array_map('trim', explode(',', $value)));
                $count = count($keywords);
                if ($count < 3 || $count > 5) {
                    $fail('Please provide between 3 and 5 keywords separated by commas. (Current count: ' . $count . ')');
                }
            }],
            'track_id' => ['required', 'exists:tracks,id'],
            'sub_track_id' => ['required', 'exists:sub_tracks,id'],
            'is_corresponding_author' => ['required', 'boolean'],
            'consent_original' => ['accepted'],
            'consent_review' => ['accepted'],
            'consent_acceptance' => ['accepted'],
            'consent_no_late_addition' => ['accepted'],
            'co_authors.*.name' => ['required', 'string', 'max:255', $noPhpTags],
            'co_authors.*.email' => ['required', 'email', 'max:255'],
            'co_authors.*.designation' => ['required', 'string', 'max:255', $noPhpTags],
            'co_authors.*.institution' => ['required', 'string', 'max:255', $noPhpTags],
            'co_authors.*.country_id' => ['required', 'exists:countries,id'],
        ], [
            'regex' => 'The :attribute contains forbidden characters (PHP tags are not allowed).',
        ]);

        try {
            DB::beginTransaction();

            // Generate Submission ID
            $submissionId = \App\Services\IdGeneratorService::generateSubmissionId();
            $hasCoAuthors = $request->has('co_authors') && count($request->co_authors) > 1;
            $paper = Paper::create([
                'user_id' => $user->id,
                'submission_id' => $submissionId,
                'title' => $request->paper_title,
                'abstract' => $request->abstract_text,
                'keywords' => $request->keywords,
                'track_id' => $request->track_id,
                'sub_track_id' => $request->sub_track_id,
                'mode_of_participation' => $profile->participation_mode ?? 'onsite',
                'is_corresponding_author' => $request->is_corresponding_author,
                'has_multiple_authors' => $hasCoAuthors,
            ]);

            $presentingAuthorIndex = $request->presenting_author_index ?? 0;

            if ($request->has('co_authors') && count($request->co_authors) > 0) {
                foreach ($request->co_authors as $index => $authorData) {
                    PaperAuthor::create([
                        'paper_id' => $paper->id,
                        'name' => $authorData['name'],
                        'email' => $authorData['email'],
                        'designation' => $authorData['designation'],
                        'department' => $authorData['department'] ?? 'N/A',
                        'institution' => $authorData['institution'],
                        'country_id' => $authorData['country_id'],
                        'author_order' => $index + 1,
                        'is_presenting_author' => ($index == $presentingAuthorIndex) ? 1 : 0,
                    ]);
                }
            } else {
                // Fallback if no authors submitted from frontend
                PaperAuthor::create([
                    'paper_id' => $paper->id,
                    'name' => trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')) ?: $user->name,
                    'designation' => $profile->designation ?? null,
                    'department' => $profile->department ?? null,
                    'institution' => $profile->institution ?? null,
                    'country_id' => $profile->country_id ?? null,
                    'email' => $user->email,
                    'author_order' => 1,
                    'is_presenting_author' => 1,
                ]);
            }

            DB::commit();

            // Recalculate and sync the total due amount on the profile
            \App\Services\PricingService::updateProfileTotalDue($profile->fresh());

            // Send submission confirmation email
            try {
                Mail::to($user->email)->queue(new AbstractSubmitted($paper));
            } catch (\Exception $e) {
                Log::error('Abstract submission email failed: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'paper_id' => $paper->id,
                ]);
            }

            return redirect()->route('papers.index')->with('message', 'Abstract submitted successfully. Submission ID: ' . $submissionId);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paper Submission Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Error submitting abstract. Please try again or contact support. Details: ' . $e->getMessage());
        }
    }

    public function edit(Paper $paper)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->roles->contains('id', 3)) {
            if ($paper->user_id !== $user->id || $paper->status !== 'pending') {
                abort(Response::HTTP_FORBIDDEN, '403 Forbidden - Paper is not editable.');
            }
        } else {
            abort_if(Gate::denies('paper_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $tracks = Track::with('subTracks')->get();
        $countries = Country::where('is_active', 1)->orderBy('name', 'asc')->get();
        $paper->load('authors');

        return view('admin.papers.edit', compact('paper', 'tracks', 'countries'));
    }

    public function update(Request $request, Paper $paper)
    {
        $user = Auth::user();

        // Authorization check
        if ($user->roles->contains('id', 3)) {
            if ($paper->user_id !== $user->id || $paper->status !== 'pending') {
                abort(Response::HTTP_FORBIDDEN, '403 Forbidden - Paper is not editable.');
            }
        } else {
            abort_if(Gate::denies('paper_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $noPhpTags = 'regex:/^((?!(<\?php|<\?|\?>)).)*$/is';

        $request->validate([
            'paper_title' => ['required', 'string', 'max:255', $noPhpTags],
            'abstract_text' => ['required', 'string', $noPhpTags, function ($attribute, $value, $fail) {
                // Simplified word count logic to match RegisterController logic
                $wordCount = !empty(trim($value)) ? preg_match_all('/\s+/', trim($value)) + 1 : 0;
                if ($wordCount > 300) {
                    $fail('The abstract must not exceed 300 words. (Current count: ' . $wordCount . ')');
                }
            }],
            'keywords' => ['required', 'string', 'max:255', $noPhpTags, function ($attribute, $value, $fail) {
                $keywords = array_filter(array_map('trim', explode(',', $value)));
                if (count($keywords) < 3 || count($keywords) > 5) {
                    $fail('Please provide between 3 and 5 keywords.');
                }
            }],
            'track_id' => 'required|exists:tracks,id',
            'sub_track_id' => 'required|exists:sub_tracks,id',
            'is_corresponding_author' => 'required|boolean',
            'presenting_author_index' => 'nullable|integer',
            'co_authors' => 'nullable|array',
            'co_authors.*.id' => 'nullable|integer|exists:paper_authors,id',
            'co_authors.*.name' => 'required|string|max:255',
            'co_authors.*.email' => 'required|email|max:255',
            'co_authors.*.designation' => 'required|string|max:255',
            'co_authors.*.department' => 'required|string|max:255',
            'co_authors.*.institution' => 'required|string|max:255',
            'co_authors.*.country_id' => 'required|exists:countries,id',
        ]);

        try {
            DB::beginTransaction();

            $hasCoAuthors = !empty($request->co_authors) && count($request->co_authors) > 1;

            $paper->update([
                'title' => $request->paper_title,
                'abstract' => $request->abstract_text,
                'keywords' => $request->keywords,
                'track_id' => $request->track_id,
                'sub_track_id' => $request->sub_track_id,
                'is_corresponding_author' => $request->is_corresponding_author,
                'has_multiple_authors' => $hasCoAuthors,
            ]);

            $primaryEmail = $paper->user->email;
            $profile = $paper->user->profile;
            $incomingIds = [];
            $orderOffset = 1;

            $presentingAuthorIndex = $request->presenting_author_index ?? 0;

            // 1. Maintain primary author robustly (update if available, create if missing somehow)
            $primaryAuthorFromForm = collect($request->co_authors ?? [])->firstWhere('email', $primaryEmail);
            $primaryAuthorIndexInForm = $primaryAuthorFromForm ? array_search((object)$primaryAuthorFromForm, json_decode(json_encode($request->co_authors), true)) : 0;

            $primaryData = [
                'name' => $primaryAuthorFromForm['name'] ?? trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')) ?: $paper->user->name,
                'designation' => $primaryAuthorFromForm['designation'] ?? ($profile->designation ?? 'N/A'),
                'department' => $primaryAuthorFromForm['department'] ?? ($profile->department ?? 'N/A'),
                'institution' => $primaryAuthorFromForm['institution'] ?? ($profile->institution ?? 'N/A'),
                'country_id' => $primaryAuthorFromForm['country_id'] ?? ($profile->country_id ?? 1),
                'email' => $primaryEmail,
                'author_order' => $orderOffset++,
                'is_presenting_author' => ($presentingAuthorIndex == $primaryAuthorIndexInForm) ? 1 : 0,
            ];

            $primaryAuthorModel = $paper->authors()->where('email', $primaryEmail)->first();
            if ($primaryAuthorModel) {
                $primaryAuthorModel->update($primaryData);
                $incomingIds[] = $primaryAuthorModel->id;
            } else {
                $newPrimary = $paper->authors()->create($primaryData);
                $incomingIds[] = $newPrimary->id;
            }

            // 2. Sync Co-authors gracefully
            if (!empty($request->co_authors)) {
                foreach ($request->co_authors as $index => $authorData) {
                    if ($authorData['email'] === $primaryEmail) {
                        continue;
                    }

                    $coAuthorData = [
                        'name' => $authorData['name'],
                        'email' => $authorData['email'],
                        'designation' => $authorData['designation'],
                        'department' => $authorData['department'] ?? 'N/A',
                        'institution' => $authorData['institution'],
                        'country_id' => $authorData['country_id'],
                        'author_order' => $orderOffset++,
                        'is_presenting_author' => ($presentingAuthorIndex == $index) ? 1 : 0,
                    ];

                    $authorId = $authorData['id'] ?? null;
                    if ($authorId) {
                        $existingAuthor = $paper->authors()->find($authorId);
                        if ($existingAuthor) {
                            $existingAuthor->update($coAuthorData);
                            $incomingIds[] = $existingAuthor->id;
                            continue;
                        }
                    }

                    // Create new if no match or no ID provided
                    $newCoAuthor = $paper->authors()->create($coAuthorData);
                    $incomingIds[] = $newCoAuthor->id;
                }
            }

            // 3. Delete any strictly removed authors from the database (cleanup orphans)
            $paper->authors()->whereNotIn('id', $incomingIds)->delete();
            DB::commit();

            return redirect()->route('papers.index')->with('success', 'Paper updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paper Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating abstract. Please try again.');
        }
    }

    public function review(Request $request, Paper $paper)
    {
        abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_note' => 'nullable|string'
        ]);

        $paper->update([
            'status' => $request->status,
            'review_note' => $request->review_note,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        \App\Models\PaperReview::create([
            'paper_id' => $paper->id,
            'reviewed_by' => Auth::id(),
            'status' => $request->status,
            'review_note' => $request->review_note,
        ]);

        // Reload the paper with the reviewer relations
        $paper->load('reviewer');

        // Send Email Notification (queued)
        try {
            if ($request->status == 'approved') {
                Mail::to($paper->user->email)->queue(new AbstractAccepted($paper));
                $message = 'Abstract approved and notification email queued for author.';
            } else {
                Mail::to($paper->user->email)->queue(new AbstractRejected($paper));
                $message = 'Abstract rejected and notification email queued for author.';
            }
        } catch (\Exception $e) {
            Log::error("Review email sending error ({$request->status}): " . $e->getMessage());
            $message = "Status updated but email queuing failed: " . $e->getMessage();
        }

        return back()->with('message', $message);
    }

    public function approve(Paper $paper)
    {
        // Old action endpoint, can be redirected/handled
        abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return back()->with('error', 'Please use the review modal to approve papers.');
    }

    public function reject(Paper $paper)
    {
        // Old action endpoint, can be redirected/handled
        abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return back()->with('error', 'Please use the review modal to reject papers.');
    }
}
