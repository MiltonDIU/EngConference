<?php

namespace App\Http\Controllers\Admin;

use App\Models\Amenity;
use App\Models\Domain;
use App\Models\EventActivity;
use App\Models\Post;
use App\Models\Paper;
use App\Models\Payment;
use App\Models\Track;
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
        $totalSubmitters = $totalAuthors;
        $totalActualAuthors = \App\Models\PaperAuthor::whereHas('paper')->count();
        $paidParticipants = Profile::where('is_author', false)->where('payment_status', '1')->count();
        $paid = Profile::where('payment_status', '1')->count();
        $unpaid = $total - $paid;

        $profiles = [
            ['country' => 'Paid', 'litres' => $paid],
            ['country' => 'Unpaid', 'litres' => $unpaid],
        ];

        // Payment Statistics Grouped by Currency and User Type (Using verified gateway payments for paid amounts)
        $profileStats = DB::table('profiles')
            ->selectRaw('currency,
                         COUNT(*) as total_users,
                         SUM(CASE WHEN payment_status = "0" THEN pay_amount ELSE 0 END) as unpaid_amount,
                         SUM(CASE WHEN is_author = 1 AND payment_status = "0" THEN pay_amount ELSE 0 END) as author_unpaid_amt,
                         SUM(CASE WHEN is_author = 0 AND payment_status = "0" THEN pay_amount ELSE 0 END) as participant_unpaid_amt')
            ->whereNotNull('currency')
            ->groupBy('currency')
            ->get()
            ->keyBy('currency');

        $actualPayments = Payment::where('status', 1)->with('user.profile')->get();
        $paidByCurrency = [
            'BDT' => ['author' => 0, 'participant' => 0],
            'INR' => ['author' => 0, 'participant' => 0],
            'USD' => ['author' => 0, 'participant' => 0],
            'EUR' => ['author' => 0, 'participant' => 0],
        ];

        foreach ($actualPayments as $p) {
            $msg = json_decode($p->message, true);
            $d = $msg['data'] ?? $msg;
            $val_c = strtoupper(trim((string)($d['value_c'] ?? '')));
            $val_b = (float)($d['value_b'] ?? 0);

            if (in_array($val_c, ['USD', 'INR', 'EUR'])) {
                $curr = $val_c;
                $amt = $val_b;
            } else {
                $curr = 'BDT';
                $amt = (float)($d['base_fair'] ?? $d['amount'] ?? $p->amount);
            }

            $isAuthor = $p->user && $p->user->profile && $p->user->profile->is_author;
            if (!isset($paidByCurrency[$curr])) {
                $paidByCurrency[$curr] = ['author' => 0, 'participant' => 0];
            }
            if ($isAuthor) {
                $paidByCurrency[$curr]['author'] += $amt;
            } else {
                $paidByCurrency[$curr]['participant'] += $amt;
            }
        }

        $currencyStats = collect(['BDT', 'INR', 'USD', 'EUR'])->map(function($curr) use ($profileStats, $paidByCurrency) {
            $pStat = $profileStats->get($curr);
            $paid = $paidByCurrency[$curr] ?? ['author' => 0, 'participant' => 0];
            $authorPaid = $paid['author'];
            $participantPaid = $paid['participant'];
            $totalPaid = $authorPaid + $participantPaid;
            $authorUnpaid = (float)($pStat->author_unpaid_amt ?? 0);
            $participantUnpaid = (float)($pStat->participant_unpaid_amt ?? 0);
            $totalUnpaid = (float)($pStat->unpaid_amount ?? 0);

            return (object)[
                'currency' => $curr,
                'total_users' => $pStat->total_users ?? 0,
                'author_paid_amt' => $authorPaid,
                'author_unpaid_amt' => $authorUnpaid,
                'participant_paid_amt' => $participantPaid,
                'participant_unpaid_amt' => $participantUnpaid,
                'paid_amount' => $totalPaid,
                'unpaid_amount' => $totalUnpaid
            ];
        });

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

        // 1. Country-wise Registration & Submission Analytics
        $countryStats = DB::table('countries')
            ->join('profiles', 'profiles.country_id', '=', 'countries.id')
            ->selectRaw('countries.id as country_id,
                         countries.name as country_name,
                         COUNT(profiles.id) as total_registrations,
                         SUM(CASE WHEN profiles.is_author = 1 THEN 1 ELSE 0 END) as total_authors,
                         SUM(CASE WHEN profiles.is_author = 1 AND profiles.payment_status = "1" THEN 1 ELSE 0 END) as paid_authors,
                         SUM(CASE WHEN profiles.is_author = 0 THEN 1 ELSE 0 END) as total_participants,
                         SUM(CASE WHEN profiles.is_author = 0 AND profiles.payment_status = "1" THEN 1 ELSE 0 END) as paid_participants,
                         SUM(CASE WHEN profiles.payment_status = "1" THEN 1 ELSE 0 END) as total_paid')
            ->groupBy('countries.id', 'countries.name')
            ->orderBy('total_registrations', 'desc')
            ->get();

        $paperCountryStats = DB::table('papers')
            ->join('profiles', 'profiles.user_id', '=', 'papers.user_id')
            ->selectRaw('profiles.country_id, COUNT(papers.id) as total_papers')
            ->groupBy('profiles.country_id')
            ->pluck('total_papers', 'profiles.country_id');

        foreach ($countryStats as $stat) {
            $stat->total_papers = $paperCountryStats[$stat->country_id] ?? 0;
            $stat->payment_percentage = $stat->total_registrations > 0
                ? round(($stat->total_paid / $stat->total_registrations) * 100, 1)
                : 0;
        }

        // 2. Daily Trends (Last 30 Days)
        $dailyRegistrations = DB::table('profiles')
            ->selectRaw('DATE(created_at) as reg_date,
                         SUM(CASE WHEN is_author = 1 THEN 1 ELSE 0 END) as author_count,
                         SUM(CASE WHEN is_author = 0 THEN 1 ELSE 0 END) as participant_count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('reg_date')
            ->orderBy('reg_date', 'asc')
            ->get();

        $dailyPapers = DB::table('papers')
            ->selectRaw('DATE(created_at) as submit_date, COUNT(*) as paper_count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('submit_date')
            ->orderBy('submit_date', 'asc')
            ->pluck('paper_count', 'submit_date');

        $dailyTrends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $reg = $dailyRegistrations->firstWhere('reg_date', $date);

            $dailyTrends[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'authors' => $reg ? (int)$reg->author_count : 0,
                'participants' => $reg ? (int)$reg->participant_count : 0,
                'papers' => (int)($dailyPapers[$date] ?? 0),
            ];
        }

        return view('admin.home', compact(
            'settings', 'profiles', 'total', 'totalParticipants', 'totalAuthors', 'totalSubmitters', 'totalActualAuthors', 'paidParticipants', 'schedules', 'allSchedules', 'blogs',
            'eventActivities', 'aminities', 'topTracks', 'totalTaka',
            'totalPayAmount', 'allowedDomain', 'currencyStats',
            'totalPapers', 'pendingPapers', 'approvedPapers', 'rejectedPapers',
            'paperStats', 'paidPapers', 'paperPaymentStats',
            'unpaidPapers', 'countryStats', 'dailyTrends'
        ));
    }

    public function tracksReport()
    {
        abort_if(Gate::denies('track_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Fetch tracks and sub-tracks submission statistics
        $reportData = DB::table('tracks')
            ->join('sub_tracks', 'sub_tracks.track_id', '=', 'tracks.id')
            ->leftJoin('papers', function($join) {
                $join->on('papers.track_id', '=', 'tracks.id')
                     ->on('papers.sub_track_id', '=', 'sub_tracks.id')
                     ->whereNull('papers.deleted_at'); // Filter out soft-deleted papers
            })
            ->selectRaw('
                tracks.id as track_id,
                tracks.name as track_name,
                sub_tracks.id as sub_track_id,
                sub_tracks.name as sub_track_name,
                COUNT(papers.id) as total_submissions,
                SUM(CASE WHEN papers.status = "approved" AND papers.payment_status = "1" THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN papers.status = "approved" AND (papers.payment_status = "0" OR papers.payment_status IS NULL) THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN papers.status = "pending" THEN 1 ELSE 0 END) as pending_count,
                (SELECT COUNT(*)
                 FROM paper_authors
                 JOIN papers ON papers.id = paper_authors.paper_id
                 WHERE papers.track_id = tracks.id
                   AND papers.sub_track_id = sub_tracks.id
                   AND papers.deleted_at IS NULL) as total_authors,
                (SELECT COUNT(DISTINCT papers.user_id)
                 FROM papers
                 WHERE papers.track_id = tracks.id
                   AND papers.sub_track_id = sub_tracks.id
                   AND papers.deleted_at IS NULL) as unique_submitters
            ')
            ->groupBy('tracks.id', 'tracks.name', 'sub_tracks.id', 'sub_tracks.name')
            ->orderBy('tracks.name', 'asc')
            ->orderBy('sub_tracks.name', 'asc')
            ->get();

        // Get all unique currencies dynamically from papers table
        $currencies = DB::table('papers')
            ->whereNotNull('currency')
            ->where('currency', '!=', '')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('currency')
            ->toArray();

        // Fallback default list of currencies if empty
        if (empty($currencies)) {
            $currencies = ['BDT', 'USD', 'EUR', 'INR'];
        }
        sort($currencies);

        // Query payment amounts grouped by track, sub-track, and currency
        $paymentSums = DB::table('papers')
            ->selectRaw('track_id, sub_track_id, currency, SUM(pay_amount) as total_amount')
            ->where('payment_status', '1')
            ->whereNull('deleted_at')
            ->groupBy('track_id', 'sub_track_id', 'currency')
            ->get();

        foreach ($reportData as $row) {
            $amounts = $paymentSums->where('track_id', $row->track_id)
                                   ->where('sub_track_id', $row->sub_track_id);

            $formattedAmounts = [];
            foreach ($amounts as $amt) {
                if ($amt->total_amount > 0 && !empty($amt->currency)) {
                    $formattedAmounts[] = number_format($amt->total_amount, 0) . ' ' . $amt->currency;
                }
            }
            $row->paid_amount = !empty($formattedAmounts) ? implode(', ', $formattedAmounts) : '0';

            $rowCurrencies = [];
            foreach ($currencies as $currency) {
                $amt = $amounts->firstWhere('currency', $currency);
                $rowCurrencies[$currency] = $amt ? $amt->total_amount : 0;
            }
            $row->currency_amounts = $rowCurrencies;
        }

        // Get track list for filters with paper counts
        $tracks = \App\Models\Track::withCount('papers')->orderBy('name', 'asc')->get();

        return view('admin.reports.tracks', compact('reportData', 'tracks', 'currencies'));
    }

    public function paperPaymentsReport(Request $request)
    {
        abort_if(!Gate::check('payment_report') && !Gate::check('track_report') && !Gate::check('admin_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // 1. Fetch successful payments
        $payments = Payment::where('status', 1)->get()->keyBy('reff_id');

        // 2. Fetch completed/processing orders
        $orders = DB::table('orders')->whereIn('status', ['Processing', 'Complete'])->get();

        // 3. Build map of paper_id => payment details and calculate currency totals
        $paperPaymentMap = [];
        $totalBaseFairBDT = 0;
        $totalAdditionChargeBDT = 0;
        $currencySummary = [
            'BDT' => ['count' => 0, 'amount' => 0, 'symbol' => 'BDT'],
            'INR' => ['count' => 0, 'amount' => 0, 'symbol' => 'INR'],
            'USD' => ['count' => 0, 'amount' => 0, 'symbol' => 'USD'],
            'EUR' => ['count' => 0, 'amount' => 0, 'symbol' => 'EUR'],
        ];

        foreach ($orders as $order) {
            $p = $payments->get($order->transaction_id);
            if (!$p) continue;

            $msg = json_decode($p->message, true);
            $d = $msg['data'] ?? $msg;

            $baseFair = (float)($d['base_fair'] ?? $d['amount'] ?? $p->amount);
            $additionCharge = (float)($d['addition_charge'] ?? 0);
            $val_b = $d['value_b'] ?? null;
            $val_c = $d['value_c'] ?? null;
            $val_d = $d['value_d'] ?? null;

            $val_c_str = strtoupper(trim((string)$val_c));
            if (in_array($val_c_str, ['USD', 'INR', 'EUR'])) {
                // Foreign currency transaction with conversion details
                $origCurr = $val_c_str;
                $origAmt = (float)$val_b;
                $rate = (float)$val_d;
            } else {
                // BDT local currency transaction (val_b/c/d are user_id or gateway internals, not currency rates)
                $origCurr = 'BDT';
                $origAmt = (float)($d['base_fair'] ?? $d['amount'] ?? $p->amount);
                $rate = 1.0;
            }

            $pIds = json_decode($order->paper_ids, true);
            if (is_array($pIds) && count($pIds) > 0) {
                $count = count($pIds);
                foreach ($pIds as $pid) {
                    $paperPaymentMap[$pid] = [
                        'payment_id' => $p->id,
                        'tran_id' => $d['tran_id'] ?? $p->reff_id,
                        'bank_tran_id' => $d['bank_tran_id'] ?? null,
                        'val_id' => $d['val_id'] ?? null,
                        'tran_date' => $d['tran_date'] ?? ($p->created_at ? $p->created_at->format('Y-m-d H:i:s') : null),
                        'card_type' => $d['card_type'] ?? null,
                        'card_brand' => $d['card_brand'] ?? null,
                        'base_fair' => $baseFair / $count,
                        'addition_charge' => $additionCharge / $count,
                        'orig_amount' => $origAmt / $count,
                        'orig_currency' => $origCurr,
                        'exchange_rate' => $rate,
                        'gateway' => $p->getaway
                    ];
                }
            }

            $totalBaseFairBDT += $baseFair;
            $totalAdditionChargeBDT += $additionCharge;
            if (!isset($currencySummary[$origCurr])) {
                $currencySummary[$origCurr] = ['count' => 0, 'amount' => 0, 'symbol' => $origCurr];
            }
            $currencySummary[$origCurr]['count']++;
            $currencySummary[$origCurr]['amount'] += $origAmt;
        }

        // 4. Eager load paid papers with authors and user profile
        $papers = Paper::where('payment_status', '1')
            ->with(['user.profile.country', 'authors.country', 'track', 'subTrack'])
            ->latest('id')
            ->get();

        // 5. Aggregate summary stats
        $uniqueUsersCount = $papers->pluck('user_id')->unique()->count();
        $totalPaidPapersCount = $papers->count();

        $totalAuthorMembersCount = 0;
        $uniqueAuthorEmails = [];
        foreach ($papers as $paper) {
            $totalAuthorMembersCount += $paper->authors->count();
            foreach ($paper->authors as $a) {
                if ($a->email) {
                    $uniqueAuthorEmails[strtolower(trim($a->email))] = true;
                } else {
                    $uniqueAuthorEmails[strtolower(trim($a->name))] = true;
                }
            }
        }
        $uniqueAuthorsCount = count($uniqueAuthorEmails);

        // Dashboard Profile Table Financial Overview
        $dashboardCurrencyStats = DB::table('profiles')
            ->selectRaw('currency,
                         COUNT(*) as total_users,
                         SUM(CASE WHEN payment_status IN ("1", "2") THEN pay_amount ELSE 0 END) as paid_amount,
                         SUM(CASE WHEN payment_status = "0" THEN pay_amount ELSE 0 END) as unpaid_amount,
                         SUM(CASE WHEN is_author = 1 AND payment_status IN ("1", "2") THEN pay_amount ELSE 0 END) as author_paid_amt,
                         SUM(CASE WHEN is_author = 1 AND payment_status = "0" THEN pay_amount ELSE 0 END) as author_unpaid_amt,
                         SUM(CASE WHEN is_author = 0 AND payment_status IN ("1", "2") THEN pay_amount ELSE 0 END) as participant_paid_amt,
                         SUM(CASE WHEN is_author = 0 AND payment_status = "0" THEN pay_amount ELSE 0 END) as participant_unpaid_amt')
            ->whereNotNull('currency')
            ->groupBy('currency')
            ->get()
            ->keyBy('currency');

        $tracks = Track::orderBy('name', 'asc')->get();

        return view('admin.reports.paper_payments', compact(
            'dashboardCurrencyStats',
            'papers',
            'paperPaymentMap',
            'uniqueUsersCount',
            'totalPaidPapersCount',
            'totalAuthorMembersCount',
            'uniqueAuthorsCount',
            'totalBaseFairBDT',
            'totalAdditionChargeBDT',
            'currencySummary',
            'tracks'
        ));
    }

}
