<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EventNotifyJobs;
use App\Models\CustomMail;
use App\Models\Domain;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\User; // Import the User model or adjust the namespace as needed
use Illuminate\Support\Facades\DB; // Import the DB facade

class MailController extends Controller
{
    public function sendMail(Request $request){

        $request->validate([
            'email_id' => 'required',
            'user_groups' => 'required',
            'den_users' => 'required'
        ]);
        $message = CustomMail::findOrFail($request->input('email_id'));

//        if ($request->input('den_users') == '0'){
//            $emailDomainsToExclude = Domain::where('status',1)->pluck('domain_name')->toArray();
//            $profiles = User::join('profiles', 'users.id', '=', 'profiles.user_id')
//                ->whereNotIn(DB::raw('SUBSTRING_INDEX(users.email, "@", -1)'), $emailDomainsToExclude)
//                ->whereIn('profiles.payment_status', $request->input('user_groups'))
//                ->where('profiles.coupon_code',null)
//                ->select('users.*', 'profiles.*')
//                ->get();
//        }else{
//            $profiles = Profile::whereIn('payment_status',$request->input('user_groups'))->get();
//        }
        $profiles = Profile::whereIn('payment_status',$request->input('user_groups'))->get();

        $i=0;
        foreach ($profiles as $profile){
            if ($request->input('den_users') == '0'){
                $emailDomainsToExclude = Domain::where('status',1)->pluck('domain_name')->toArray();
                if ($profile->user){
                    $domain = explode('@', $profile->user->email);
                    if ($profile->user->email != null and $profile->coupon_code == null and $profile->special_coupon != null and !in_array($domain[1], $emailDomainsToExclude)){
                        EventNotifyJobs::dispatch($profile->user,$message);
                    }
                }

            }else{
                if ($profile->user){
                    EventNotifyJobs::dispatch($profile->user,$message);
                }
            }
        }
        return response('Email sent successfully');

    }
}
