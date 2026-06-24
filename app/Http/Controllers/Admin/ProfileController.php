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
use App\Models\Country;
use Carbon\Carbon;
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
        $loged = Auth::user();
        
        // Auto-recalculate unpaid participant/author registration totals when visiting the dashboard
        if ($loged && $loged->roles->contains('id', 3)) {
            $myProfile = Profile::where('user_id', $loged->id)->first();
            if ($myProfile && $myProfile->payment_status != '1') {
                \App\Services\PricingService::updateProfileTotalDue($myProfile);
            }
        }

        $user = auth()->user()->roles->contains(3);
        if ($user === true){
            $emails = 'Null';
            $profiles = Profile::where('user_id',$loged->id)->with(['user.papers.authors', 'country'])->get();
        }else{
            $emails = CustomMail::where('publication_status',1)->get();
            $profiles = Profile::with(['user.papers.authors', 'country'])->orderBy('registration_id','asc')->get();
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
        $countries = Country::all();
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
        return view('admin.profile.edit-profile',compact('profile','user','schedules','workshops', 'countries'));
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'whatsapp_number' => 'required|string|max:20',
            'participation_mode' => 'required|in:onsite,online',
            'id' => 'required|exists:profiles,id',
            'author_list_confirmed' => 'nullable|boolean',
        ]);
        
        $profile = Profile::find($request->id);
        
        // Update user name as well for consistency
        $user = $profile->user;
        $user->update([
            'name' => $request->first_name . ' ' . $request->last_name
        ]);

        $profileData = $request->only([
            'first_name', 'last_name', 'designation', 'department', 'institution', 
            'country_id', 'whatsapp_number', 'registration_id', 'is_author', 
            'participation_mode', 'pay_amount', 'currency', 'payment_status', 'coupon_code',
            'author_list_confirmed'
        ]);
        
        $userSchedule = $request->input('schedule_ids', []);

        $profile->update($profileData);

        // If payment status changed to complete and no registration ID exists, generate one
        if ($profile->payment_status == 1 && $profile->registration_id == null) {
            $profile->registration_id = \App\Services\IdGeneratorService::generateRegistrationId();
            $profile->save();
        }

        $user->schedules()->sync($userSchedule);

        return redirect('show/profile')->with('message','Profile updated successfully');
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
            $profiles = Profile::where('user_id',$loged->id)->with(['user.papers.authors', 'country'])->get();
        }else{
            $emails = CustomMail::where('publication_status',1)->get();
            $profiles = Profile::where('user_id',1261)->with(['user.papers.authors', 'country'])->get();
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

    public function recalculateFee(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // If a profile_id is provided and the logged-in user is not a participant (role 3)
        if ($request->has('profile_id') && !$user->roles->contains('id', 3)) {
            $profile = Profile::find($request->profile_id);
        } else {
            $profile = Profile::where('user_id', $user->id)->first();
        }

        if (!$profile) {
            return redirect()->back()->with('error', 'Profile not found.');
        }

        if ($profile->payment_status == '1') {
            return redirect()->back()->with('error', 'Recalculation not allowed for completed payments.');
        }

        \App\Services\PricingService::updateProfileTotalDue($profile);

        return redirect()->back()->with('message', 'Registration fee recalculated successfully according to the current pricing timeline.');
    }

    public function recalculateAllUnpaid(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->roles->contains('id', 3)) {
            abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
        }

        $unpaidProfiles = Profile::where('payment_status', '<>', '1')->get();
        
        $count = 0;
        foreach ($unpaidProfiles as $profile) {
            \App\Services\PricingService::updateProfileTotalDue($profile);
            $count++;
        }

        return redirect()->back()->with('message', $count . ' unpaid profile fees recalculated successfully.');
    }

    public function confirmStudentStatus(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = Profile::where('user_id', $user->id)->first();
        if (!$profile) {
            return redirect()->back()->with('error', 'Profile not found.');
        }

        $request->validate([
            'authors' => 'required|array',
            'authors.*.id' => 'required|exists:paper_authors,id',
            'authors.*.is_student' => 'required|in:0,1'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($request->authors as $authorData) {
                $author = \App\Models\PaperAuthor::where('id', $authorData['id'])
                    ->whereHas('paper', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->first();

                if ($author) {
                    $author->is_student = (bool)$authorData['is_student'];
                    $author->save();
                }
            }

            $profile->author_list_confirmed = true;
            $profile->save();

            // Recalculate total due based on the new student status
            \App\Services\PricingService::updateProfileTotalDue($profile);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('message', 'Author list and student status confirmed successfully. Your registration fee has been updated.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Confirm Student Status Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to confirm student status. Please try again.');
        }
    }
}
