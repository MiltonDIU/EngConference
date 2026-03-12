<?php

namespace App\Http\Controllers\Admin;

use App\Models\Domain;
use App\Models\Schedule;
use Gate;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CustomMail;
use App\Models\Profile;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
    {
        $user = auth()->user()->roles->contains(3);
        $loged = Auth::user();
        if ($user === true){
            $emails = 'Null';
            $profiles = Profile::where('user_id',$loged->id)->get();
        }else{
            $emails = CustomMail::where('publication_status',1)->get();
            $profiles = Profile::orderBy('identity_no','asc')->get();
        }
        $settings = Setting::pluck('value', 'key');

        $allowedDomain = Domain::where('status',1)->pluck('domain_name')->toArray();


        return view('admin.profile.show-profile',[
            'profiles'=>$profiles,
            'emails' => $emails,
            'settings'=>$settings,
            'allowedDomain'=>$allowedDomain,
        ]);
    }

    public function paymentComplete(){
        $profiles = Profile::where('payment_status','1')->get();
        return view('admin.profile.profile-status',['profiles'=>$profiles]);
    }

    public function paymentNotComplete(){
        $profiles = Profile::where('payment_status','<>','1')->get();
        return view('admin.profile.profile-status',['profiles'=>$profiles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settings = Setting::pluck('value', 'key');
        return view('main.close',['settings'=>$settings]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone'=>'required|unique:profiles,phone',
            'gender' => 'required',
            'institute_name' => 'required',
            'academic_major' => 'required',
            'part_aws_cloud_club' => 'required',
            'tracks_like' => 'required',
            'aws_familiar' => 'required',
            'comments' => 'required'
        ]);
        $user = Auth::user();
        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->phone = $request->phone;
        $profile->gender = $request->gender;
        $profile->institute_name = $request->institute_name;
        $profile->academic_major = $request->academic_major;
        $profile->part_aws_cloud_club = $request->part_aws_cloud_club;
        $profile->tracks_like = $request->tracks_like;
        $profile->aws_familiar = $request->aws_familiar;
        $profile->comments = $request->comments;
        $profile->payment_status = '0';
        $profile->production_app = $request->production_app;
        $profile->application_url = $request->application_url;
        $profile->logo_url = $request->logo_url;
        $profile->save();
        return redirect('show/profile')->with('message','Registere Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validateCoupon(Request $request)
    {

        $couponCode = $request->input('coupon_code');
        $email = $request->input('email');
        // Check if the coupon code and email combination exists in the coupon table
//        $coupon = Coupon::where('title', $couponCode)->where('email', $email)->first();
        $coupon = Coupon::where('title', $couponCode)
            ->where('expire_date', '>=', now())
            ->first();
        if ($coupon) {
            if ($coupon->is_domain == 1) {
                if ($email == null) {
                    return response()->json(['valid' => false,'message'=>'It\'s InValid: This coupon code is not valid']);
                }
                $domain = explode('@', $email);
                $allowedDomain = Domain::where('status',1)->pluck('domain_name')->toArray();
                if (!in_array($domain[1],$allowedDomain)) {
                    return response()->json(['valid' => false,'message'=>'It\'s InValid: This email is not allowed to use this coupon code']);
                }else{
                    // Valid coupon code and email combination
                    return response()->json(['valid' => true,'message'=>'Coupon Applied Successfully']);
                }

            }else{
                // Valid coupon code and email combination
                return response()->json(['valid' => true,'message'=>'Coupon Applied Successfully']);
            }
        }else {
            // Invalid coupon code or email combination
            return response()->json(['valid' => false,'message'=>'It\'s InValid: This coupon code is not valid']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('profile_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $profile = Profile::find($id);
        $user = User::find($profile->user_id);
        $schedules = Schedule::with(['speaker', 'users' => function ($query) {
            $query->whereHas('profile', function ($subQuery) {
                $subQuery->where('payment_status', '1');
            });
        }])
            ->where('is_workshop', '1')
            ->orderBy('start_time', 'asc')
            ->get()
            ->filter(function ($schedule) {
                return $schedule->total_seat > $schedule->users->count();
            })
            ->groupBy('day_number');
$workshops = $user->schedules->pluck('id')->toArray();
        return view('admin.profile.edit-profile',compact('profile','user','schedules','workshops'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        abort_if(Gate::denies('profile_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'phone'=>'required',
            'institute_name' => 'required',
        ]);
        $user = Auth::user();
        $profileData = $request->only(['phone','institute_name','coupon_code','payment_status','pay_amount']);
        $userSchedule = $request->input('schedule_ids');

        $profile = Profile::find($request->id);
        $profile->update($profileData);
        $user = $profile->user;
        $user->schedules()->sync($userSchedule);

        return redirect('show/profile')->with('message','Profile update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function statusPayment(){
        $settings = Setting::pluck('value', 'key');
        return view('main.payment.payment-status',['settings'=>$settings]);
    }

    public function showProfile()
    {
        $user = auth()->user()->roles->contains(3);
        $loged = Auth::user();
        if ($user === true){
            $emails = 'Null';
            $profiles = Profile::where('user_id',$loged->id)->get();
        }else{
            $emails = CustomMail::where('publication_status',1)->get();
            $profiles = Profile::where('user_id',1261)->get();
        }
        return view('admin.profile.show-profile-test',[
            'profiles'=>$profiles,
            'emails' => $emails
        ]);
    }
    public function createProfile()
    {
        $settings = Setting::pluck('value', 'key');
        return view('main.registration-test',['settings'=>$settings]);

    }


}
