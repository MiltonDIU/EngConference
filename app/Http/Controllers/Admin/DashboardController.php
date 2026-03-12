<?php

namespace App\Http\Controllers\Admin;

use App\Models\Amenity;
use App\Models\Domain;
use App\Models\EventActivity;
use App\Models\Post;
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

    public function index(){

        abort_if(Gate::denies('admin_dashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = Auth::user();
        $allowedDomain = Domain::where('status',1)->pluck('domain_name')->toArray();
        //users registration paid and unpaid chart
        $settings = Setting::pluck('value', 'key');
        $total = Profile::get()->count();
        $paid = Profile::where('payment_status','1')->get()->count();
        $unpaid = $total - $paid;
        $profiles = array();
        $totalTaka = array();
        $totalPayAmount = \DB::table('profiles')->sum('pay_amount');
        $paidPayAmount = \DB::table('profiles')->where('payment_status','1')->sum('pay_amount');
        $unpaidAmount = $totalPayAmount - $paidPayAmount;
        array_push($totalTaka, ['country' => 'Paid', 'litres' => intval($paidPayAmount)]);
        array_push($totalTaka, ['country' => 'Unpaid', 'litres' => intval($unpaidAmount)]);
// dd($totalTaka);
        array_push($profiles, ['country' => 'Paid', 'litres' => $paid]);
        array_push($profiles, ['country' => 'Unpaid', 'litres' => $unpaid]);

//        $registrations = [];
//
//        $endDate = now(); // Get the current date and time
//        $startDate = $endDate->copy()->subDays(10); // Subtract 10 days from the current date
//
//
//        for ($i = 1; $i <= 10; $i++) {
//            $dayData = [
//                'category' => 'Nov #' . $i,
//                'first' => rand(20, 60), // Replace with your logic to get the 'first' count
//                'second' => rand(30, 80), // Replace with your logic to get the 'second' count
//                'third' => rand(20, 70), // Replace with your logic to get the 'third' count
//            ];
//
//            $registrations[] = $dayData;
//        }
//
        $endDate = now(); // Get the current date and time
        $startDate = $endDate->copy()->subDays(10); // Subtract 10 days from the current date

        $registrations = \DB::table('profiles')
            ->selectRaw('DATE_FORMAT(created_at, "%b %d") as date, SUM(CASE WHEN payment_status = "1" THEN 1 ELSE 0 END) as paid, SUM(CASE WHEN payment_status = "0" THEN 1 ELSE 0 END) as unpaid, SUM(1) as total_users')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date', 'asc') // Order by date in descending order
            ->get();
//        $registrations = \DB::table('profiles')
//            ->selectRaw('DATE_FORMAT(created_at, "%b %d") as date, SUM(CASE WHEN payment_status = 1 THEN 1 ELSE 0 END) as paid, SUM(CASE WHEN payment_status = 0 THEN 1 ELSE 0 END) as unpaid')
//            ->where('created_at', '>=', $startDate)
//            ->where('created_at', '<=', $endDate)
//            ->groupBy('date')
//            ->get();




//        {
//            category: 'Nov #1',
//                    first: 40,
//                    second: 55,
//                    third: 60
//                },

//        array_push($profiles, ['country' => 'Totla', 'litres' => $total]);
        //end of users registration paid and unpaid chart
        $schedules = Schedule::with('speaker')
            ->where('is_workshop','1')
            ->where('is_active','1')
            ->orderBy('day_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('day_number');

        $allSchedules = Schedule::with('speaker')
            ->where('is_active','1')
            ->orderBy('day_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('day_number');
        $blogs = Post::where('is_active','1')->orderBy('views','desc')->get();
        $aminities = Amenity::orderBy('id','desc')->get();
        $eventActivities = EventActivity::all();

        if(auth()->user()->roles->contains('id', 3)){
            if ($user->profile->payment_status==1 && $user->profile->identity_no==null){
                $profile = Profile::find($user->profile->id);
                $newId = $this->uniqueIdGenerate();
                $profile->identity_no = $newId;
                $profile->save();
            }
            $user = Auth::user();
        }



        return view('admin.home',compact('settings','profiles','total','schedules','allSchedules','blogs','eventActivities','aminities','registrations','totalTaka','totalPayAmount','allowedDomain'));;
    }

    public function uniqueIdGenerate(){
        $currentDate = Carbon::now();
        $formattedDate = sprintf('%02d%02d%02d', $currentDate->format('y'), $currentDate->month, $currentDate->day);
// Count existing profiles with non-zero identity numbers
        $total = Profile::where('identity_no', '!=', '0')->count();
// Increment the existing count
        $newIdCount = $total + 1;

// Pad the count with leading zeros to a specific width (e.g., 4 for "0001", 5 for "00001")
        $sequenceNumber = str_pad($newIdCount, 4, '0', STR_PAD_LEFT);
        $newId = $formattedDate . $sequenceNumber;
        return $newId;
    }
}
