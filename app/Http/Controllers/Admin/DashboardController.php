<?php

namespace App\Http\Controllers\Admin;

use App\Models\Amenity;
use App\Models\Domain;
use App\Models\EventActivity;
use App\Models\Post;
use App\Models\Paper;
use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use DB;

class DashboardController extends Controller
{
    public function index()
    {

        abort_if(Gate::denies('admin_dashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $allowedDomain = Domain::where('status', 1)->pluck('domain_name')->toArray();
        $settings = Setting::pluck('value', 'key');

        // General Registration Stats
        $total = Profile::count();
        $totalParticipants = Profile::where('is_author', false)->count();
        $totalAuthors = Profile::where('is_author', true)->count();
        $paid = Profile::where('payment_status', '1')->count();
        $unpaid = $total - $paid;

        $profiles = [
            ['country' => 'Paid', 'litres' => $paid],
            ['country' => 'Unpaid', 'litres' => $unpaid],
        ];

        // Payment Statistics Grouped by Currency and User Type
        $currencyStats = DB::table('profiles')
            ->selectRaw('currency,
                         COUNT(*) as total_users,
                         SUM(CASE WHEN payment_status = "1" THEN pay_amount ELSE 0 END) as paid_amount,
                         SUM(CASE WHEN payment_status = "0" THEN pay_amount ELSE 0 END) as unpaid_amount,
                         SUM(CASE WHEN is_author = 1 AND payment_status = "1" THEN pay_amount ELSE 0 END) as author_paid_amt,
                         SUM(CASE WHEN is_author = 1 AND payment_status = "0" THEN pay_amount ELSE 0 END) as author_unpaid_amt,
                         SUM(CASE WHEN is_author = 0 AND payment_status = "1" THEN pay_amount ELSE 0 END) as participant_paid_amt,
                         SUM(CASE WHEN is_author = 0 AND payment_status = "0" THEN pay_amount ELSE 0 END) as participant_unpaid_amt')
            ->whereNotNull('currency')
            ->groupBy('currency')
            ->get();

        $totalPayAmount = $currencyStats->sum('paid_amount'); // Still useful for general overview
        $totalTaka = $currencyStats->map(function($stat) {
            return ['country' => $stat->currency . ' (Paid)', 'litres' => intval($stat->paid_amount)];
        })->toArray();

        // Top Submission Tracks with Status Breakdown
        $topTracks = DB::table('tracks')
            ->leftJoin('papers', 'tracks.id', '=', 'papers.track_id')
            ->selectRaw('tracks.name,
                         COUNT(papers.id) as submission_count,
                         SUM(CASE WHEN papers.status = "pending" THEN 1 ELSE 0 END) as pending_count,
                         SUM(CASE WHEN papers.status = "approved" THEN 1 ELSE 0 END) as approved_count,
                         SUM(CASE WHEN papers.status = "rejected" THEN 1 ELSE 0 END) as rejected_count')
            ->groupBy('tracks.id', 'tracks.name')
            ->orderBy('submission_count', 'DESC')
            ->limit(10)
            ->get();

        // Workshop Schedules
        $schedules = Schedule::with('speaker')
            ->where('is_workshop', '1')
            ->where('is_active', '1')
            ->orderBy('day_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('day_number');

        $allSchedules = Schedule::with('speaker')
            ->where('is_active', '1')
            ->orderBy('day_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('day_number');

        $blogs = Post::where('is_active', '1')->orderBy('views', 'desc')->get();
        $aminities = Amenity::orderBy('id', 'desc')->get();
        $eventActivities = EventActivity::all();

        // Abstract Statistics
        $totalPapers = Paper::count();
        $pendingPapers = Paper::where('status', 'pending')->count();
        $approvedPapers = Paper::where('status', 'approved')->count();
        $rejectedPapers = Paper::where('status', 'rejected')->count();

        $paperStats = [
            ['category' => 'Pending', 'litres' => $pendingPapers],
            ['category' => 'Approved', 'litres' => $approvedPapers],
            ['category' => 'Rejected', 'litres' => $rejectedPapers],
        ];

        $paidPapers = Paper::where('payment_status', '1')->count();
        $unpaidPaperCount = Paper::where('status', 'approved')->where('payment_status', '0')->count();

        $paidPapers = Paper::where('payment_status', '1')->count();
        $unpaidPaperCount = Paper::where('status', 'approved')->where('payment_status', '0')->count();

        $paperPaymentStats = [
            ['category' => 'Paid', 'litres' => $paidPapers],
            ['category' => 'Unpaid', 'litres' => $unpaidPaperCount],
        ];

        // User-Specific Logic (Unpaid Papers & Identity Generation)
        $unpaidPapers = collect();
        if ($user->roles->contains('id', 3)) {
            $unpaidPapers = Paper::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->whereNull('payment_status')
                      ->orWhere('payment_status', '!=', '1');
                })->get();

            if ($user->profile && $user->profile->payment_status == 1 && $user->profile->registration_id == null) {
                $profile = Profile::find($user->profile->id);
                $profile->registration_id = \App\Services\IdGeneratorService::generateRegistrationId();
                $profile->save();
                $user = $user->fresh();
            }
        }

        return view('admin.home', compact(
            'settings', 'profiles', 'total', 'totalParticipants', 'totalAuthors', 'schedules', 'allSchedules', 'blogs',
            'eventActivities', 'aminities', 'topTracks', 'totalTaka',
            'totalPayAmount', 'allowedDomain', 'currencyStats',
            'totalPapers', 'pendingPapers', 'approvedPapers', 'rejectedPapers',
            'paperStats', 'paidPapers', 'paperPaymentStats',
            'unpaidPapers'
        ));
    }
}
