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
                $query = Paper::where('user_id', $user->id)->with('user', 'track', 'subTrack', 'authors');
            } else {
                abort_if(Gate::denies('paper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
                $query = Paper::with('user', 'track', 'subTrack', 'authors');
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
                    $editBtn = Gate::allows('paper_edit') ? '<a href="#" class="btn btn-sm btn-white border text-info" title="Edit"><i class="fas fa-edit"></i></a>' : '';

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
                                '.$payBtn.'
                            </div>';
                })
                ->editColumn('submission_id', function ($row) {
                    return '<span class="font-weight-bold text-primary">'.$row->submission_id.'</span>';
                })
                ->addColumn('authors', function ($row) {
                    $authors = $row->authors->pluck('name')->toArray();
                    if (empty($authors) && $row->user) {
                        $authors[] = $row->user->name;
                    }
                    $authorText = implode(', ', $authors);
                    return '<div class="text-muted small text-truncate" style="max-width: 200px;" title="'.$authorText.'">'.$authorText.'</div>';
                })
                ->editColumn('title', function ($row) {
                    return '<div class="text-dark font-weight-600 text-truncate" style="max-width: 350px;" title="'.$row->title.'">'.$row->title.'</div>';
                })
                ->editColumn('track', function ($row) {
                    $trackName = $row->track->name ?? 'N/A';
                    $subTrackHtml = $row->subTrack ? '<small class="text-muted d-block px-2" style="font-size: 0.7rem; line-height: 1.2;"><i class="fas fa-caret-right mr-1"></i> '.$row->subTrack->name.'</small>' : '';
                    return '<span class="badge badge-light border text-dark px-3 py-2 rounded-pill d-block mb-1 text-truncate" style="max-width: 250px;" title="'.$trackName.'">'.$trackName.'</span>' . $subTrackHtml;
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
            dd('test');
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
        $profile = Profile::where('user_id', $user->id)->first();

        $settings = Setting::pluck('value', 'key');
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

            // Generation Submission ID
            $datePart = Carbon::now()->format('Ymd');
            $lastPaper = Paper::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->first();
            $sequence = 1;
            if ($lastPaper && preg_match('/ABS-\d+-(\d+)/', $lastPaper->submission_id, $matches)) {
                $sequence = intval($matches[1]) + 1;
            }
            $submissionId = sprintf('ABS-%s-%03d', $datePart, $sequence);
            $hasCoAuthors = $request->has('co_authors') && count($request->co_authors) > 0;
            $paper = Paper::create([
                'user_id' => $user->id,
                'submission_id' => $submissionId,
                'title' => $request->paper_title,
                'abstract' => $request->abstract_text,
                'keywords' => $request->keywords,
                'track_id' => $request->track_id,
                'sub_track_id' => $request->sub_track_id,
                'mode_of_participation' => 'onsite',
                'is_corresponding_author' => $request->is_corresponding_author,
                'has_multiple_authors' => $hasCoAuthors,
            ]);

            // Add logged-in user as an author
            PaperAuthor::create([
                'paper_id' => $paper->id,
                'name' => ($profile->first_name ?? '') . ' ' . ($profile->last_name ?? ''),
                'email' => $user->email,
                'designation' => $profile->designation ?? 'Author',
                'institution' => $profile->institution ?? 'N/A',
                'country_id' => $profile->country_id ?? 1, // Fallback if needed, but should be from profile
                'is_presenting_author' => $request->is_corresponding_author,
                'author_order' => 1,
            ]);

            // Add co-authors
            if ($hasCoAuthors) {
                foreach ($request->co_authors as $index => $coAuthor) {
                    PaperAuthor::create([
                        'paper_id' => $paper->id,
                        'name' => $coAuthor['name'],
                        'email' => $coAuthor['email'],
                        'designation' => $coAuthor['designation'],
                        'institution' => $coAuthor['institution'],
                        'country_id' => $coAuthor['country_id'],
                        'is_presenting_author' => false,
                        'author_order' => $index + 2,
                    ]);
                }
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
