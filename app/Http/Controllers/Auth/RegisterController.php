<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Models\ReferralVisitor;
use App\Models\Schedule;
use App\Models\Setting;
use Carbon\Carbon;
use DB;
use App\Models\Coupon;
use App\Models\Domain;
use App\Models\Profile;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Validation\ValidationException;
use App\Rules\SchedulesPerDay;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

     public function showRegistrationForm()
    {
        return view('main.close');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
//    protected function validator(array $data)
//    {
//        return Validator::make($data, [
//            'name' => ['required', 'string', 'max:255'],
//            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//            'password' => ['required', 'string', 'min:8', 'confirmed'],
//            'phone' => ['required'],
//        ]);
//    }
    protected function validator(array $data)
    {
        // Retrieve the grouped schedules using the query
//        $groupedSchedules = Schedule::select('day_number', \DB::raw('MAX(id) as max_id'))
//            ->whereNull('deleted_at')
//            ->groupBy('day_number')
//            ->get();

        $schedules = Schedule::with('speaker')
            ->where('is_workshop', '1')
            ->orderBy('start_time', 'desc')
            ->get()
            ->filter(function ($schedule) {
                return $schedule->total_seat >  $schedule->users->count();
            })
            ->groupBy('day_number');


        $validator =  Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'institute_name' => ['required', 'string'],
            'phone' => ['required'],
            // Use the custom rule with the grouped schedules
            'schedule_ids' => ['required', 'array', new SchedulesPerDay($schedules)],
        ]);




return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $settings = Setting::pluck('value', 'key');
        try {
            DB::beginTransaction();
            $email = $data['email'];
            $domain = explode('@', $email)[1];
            $allowedDomain = Domain::where('status',1)->pluck('domain_name')->toArray();
            $coupon = $data['coupon'];
            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'password' => Hash::make($data['password']),
            ]);
            User::findOrFail($user->id)->roles()->sync(3);

           $schedule_ids = $data['schedule_ids'];
           $user->schedules()->attach($schedule_ids);
            $profile=[
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'institute_name' => $data['institute_name'],
                'payment_status' => '0',
            ];


            // Get the early registration last date and the current date
            $earlyRegistrationLastDate = Carbon::parse($settings['early_registration_last_date']);
            $currentDate = Carbon::now();
            // Check if the early registration last date is greater than the current date
            if ($earlyRegistrationLastDate->gt($currentDate)) {
                // Use the early registration event price
                $pay_amount = $settings['early_registration_event_price'];

                if ($data['radioButton']=='yes') {
                    if ($data['coupon'] != null) {
                        $coupon = Coupon::where('title', $data['coupon'])->where('expire_date', '>=', now())->first();
                        if ($coupon) {
                            $pay_amount = $pay_amount - 50;
                            $profile['coupon_code'] = $data['coupon'];
                            if ($pay_amount > 0) {
                                $profile['payment_status'] = '0';
                            } else {
                                $profile['payment_status'] = '1';
                            }
                        }
                    }
                }

            } else {
                // Use the regular event price
                $pay_amount = $settings['event_price'];
                if ($data['radioButton']=='yes') {
                    if ($data['coupon'] != null) {
                        $coupon = Coupon::where('title', $data['coupon'])->where('expire_date', '>=', now())->first();

                        if ($coupon) {
                            //$pay_amount = $pay_amount - $coupon->value;
                            if ($coupon->is_domain == 1) {
                                if (in_array($domain, $allowedDomain)) {
                                    $pay_amount = $pay_amount - $coupon->value;
                                }else{
                                    $pay_amount = $pay_amount;
                                }
                            } else {
                                $pay_amount = $pay_amount - $coupon->value;
                            }
                            $profile['coupon_code'] = $data['coupon'];
                            if ($pay_amount > 0) {
                                $profile['payment_status'] = '0';
                            } else {
                                $profile['payment_status'] = '1';
                            }
                        }
                    }
                }

            }

            if ($data['radioButton']=='no') {
                if (in_array($domain, $allowedDomain)) {
                    $pay_amount = $pay_amount - $settings['selected_domain_discount'];
                }
            }

                $profile['pay_amount'] = $pay_amount;

            $profile =  Profile::create($profile);
             if (Cookie::get('referral_visitors')!=null){
                 ReferralVisitor::where('cookie_value',Cookie::get('referral_visitors'))->first()->update(['user_id'=>$user->id]);
             }

            DB::commit();
            return $user;
        } catch (Throwable $e) {
            DB::rollback();
        }
    }
    public function register_old(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        if ($request->action == 'save-pay'){
            $payment= new PaymentController();
            $paymentValue =  $payment->setPayment($user);
            return redirect()->route('setPayment',['data' => $request])->with('message', 'New User Create Successfully');
        }else {
            return redirect('/book-ticket')->with('message', 'Registration Complete. Please complete your payment to confirm your seat. You can pay after login.');
        }
    }
     public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        if ($request->action == 'save-pay'){

         $payment= new PaymentController();
//            $paymentValue =  $payment->setPayment($user);
//            return redirect()->route('setPayment',['data' => $request])->with('message', 'New User Create Successfully');
            $randomNum= rand(100,999).'-'."aicDipti-".strtotime(now());  //substr(str_shuffle
            $payment->paymentStore($user,$randomNum,'sslcommerz');
            $sslPayment = new SslCommerzPaymentController();
            $sslPayment->index($request,$user,$randomNum);


        }else {
            return redirect('/book-ticket')->with('message', 'Registration Complete. Please complete your payment to confirm your seat. You can pay after login.');
        }
    }
}
