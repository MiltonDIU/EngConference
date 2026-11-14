<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ReviewerPaperController extends Controller
{
    /**
     * Papers assigned to the logged-in reviewer, grouped by session.
     */
    public function index()
    {
        $user = Auth::user();

        $papers = $user->reviewPapers()
            ->with('track', 'subTrack', 'authors', 'files')
            // Rows are stored in programme order, so the id keeps the sheet's order
            ->orderBy('paper_reviewers.id')
            ->get();

        abort_if($papers->isEmpty(), 403, 'No papers are assigned to you for review.');

        // Onsite and Online sessions are numbered separately, so the number alone is not unique
        $sessions = $papers->groupBy(fn ($paper) => ($paper->pivot->session_no ?? 0) . '|' . $paper->pivot->session_info);

        return view('reviewer.papers.index', compact('sessions', 'papers'));
    }
}
