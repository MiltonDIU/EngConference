<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\OneCardPaymentController;
use App\Models\ReferralVisitor;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Paper;
use App\Models\PaperAuthor;
use App\Models\Track;
use App\Models\SubTrack;
use App\Mail\AbstractSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
        $noPhpTags = 'regex:/^((?!(<\?php|<\?|\?>)).)*$/is';

        $rules = [
            'first_name' => ['required', 'string', 'max:255', $noPhpTags],
            'last_name' => ['required', 'string', 'max:255', $noPhpTags],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'designation' => ['required', 'string', 'max:255', $noPhpTags],
            'institution' => ['required', 'string', 'max:255', $noPhpTags],
            'country_id' => ['required', 'exists:countries,id'],
            'whatsapp_number' => ['required', 'string', 'max:20', $noPhpTags],
            'participation_mode' => ['required', 'in:onsite,online'],
            'is_author' => ['required', 'boolean'],
            'extra_info' => ['nullable', 'string', 'max:0'],
        ];

        if (isset($data['is_author']) && $data['is_author'] == "1") {
            $settings = Setting::pluck('value', 'key');
            $isSubmissionOpen = ($settings['is_abstract_submission_open'] ?? 'true') == 'true';

            if ($isSubmissionOpen) {
                $rules['paper_title'] = ['required', 'string', 'max:500', $noPhpTags];
                $rules['abstract_text'] = ['required', 'string', $noPhpTags, function ($attribute, $value, $fail) {
                    $wordCount = !empty(trim($value)) ? preg_match_all('/\s+/', trim($value)) + 1 : 0;
                    if ($wordCount > 300) {
                        $fail('The abstract must not exceed 300 words. (Current count: ' . $wordCount . ')');
                    }
                }];
                $rules['keywords'] = ['required', 'string', 'max:255', $noPhpTags, function ($attribute, $value, $fail) {
                    $keywords = array_filter(array_map('trim', explode(',', $value)));
                    $count = count($keywords);
                    if ($count < 3 || $count > 5) {
                        $fail('Please provide between 3 and 5 keywords separated by commas. (Current count: ' . $count . ')');
                    }
                }];
                $rules['track_id'] = ['required', 'exists:tracks,id'];
                $rules['sub_track_id'] = ['required', 'exists:sub_tracks,id'];
                $rules['is_corresponding_author'] = ['required', 'boolean'];

                // Consents
                $rules['consent_original'] = ['accepted'];
                $rules['consent_review'] = ['accepted'];
                $rules['consent_acceptance'] = ['accepted'];
                $rules['consent_no_late_addition'] = ['accepted'];

                // Co-authors if any
                if (isset($data['co_authors']) && is_array($data['co_authors'])) {
                    foreach ($data['co_authors'] as $index => $author) {
                        $rules["co_authors.$index.name"] = ['required', 'string', 'max:255', $noPhpTags];
                        $rules["co_authors.$index.email"] = ['required', 'email', 'max:255'];
                        $rules["co_authors.$index.designation"] = ['required', 'string', 'max:255', $noPhpTags];
                        $rules["co_authors.$index.institution"] = ['required', 'string', 'max:255', $noPhpTags];
                        $rules["co_authors.$index.country_id"] = ['required', 'exists:countries,id'];
                    }
                }
            }
        }

        $messages = [
            'regex' => 'The :attribute contains forbidden characters (PHP tags are not allowed).',
        ];

        return Validator::make($data, $rules, $messages);
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
        $paper = null;

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->roles()->sync(3); // Default Participant/Author role

            // 1. Create Profile
            $profileData = [
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'whatsapp_number' => $data['whatsapp_number'],
                'designation' => $data['designation'],
                'institution' => $data['institution'],
                'department' => $data['department'] ?? null,
                'country_id' => $data['country_id'],
                'is_author' => isset($data['is_author']) && $data['is_author'] == "1",
                'participation_mode' => $data['participation_mode'] ?? 'onsite',
                'registration_id' => \App\Services\IdGeneratorService::generateRegistrationId(),
                'payment_status' => '0',
            ];

            $profile = Profile::create($profileData);

            // 2. Handle Paper Submission if Author
            if ($profile->is_author && ($settings['is_abstract_submission_open'] ?? 'true') == 'true') {
                $hasCoAuthors = isset($data['co_authors']) && is_array($data['co_authors']) && count($data['co_authors']) > 0;

                $paperData = [
                    'user_id' => $user->id,
                    'submission_id' => \App\Services\IdGeneratorService::generateSubmissionId(),
                    'title' => $data['paper_title'] ?? 'N/A',
                    'abstract' => $data['abstract_text'] ?? 'N/A',
                    'keywords' => $data['keywords'] ?? 'N/A',
                    'track_id' => $data['track_id'] ?? null,
                    'sub_track_id' => $data['sub_track_id'] ?? null,
                    'mode_of_participation' => $data['participation_mode'] ?? 'onsite',
                    'is_corresponding_author' => $hasCoAuthors ? ($data['is_corresponding_author'] ?? true) : true,
                    'has_multiple_authors' => $hasCoAuthors,
                    'status' => 'pending',
                    'payment_status' => '0',
                ];

                $paper = Paper::create($paperData);

                // Add the registering user as the first author
                $paper->authors()->create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'designation' => $profile->designation,
                    'institution' => $profile->institution,
                    'country_id' => $profile->country_id,
                    'is_presenting_author' => $hasCoAuthors ? ($data['is_corresponding_author'] ?? true) : true,
                    'author_order' => 1,
                ]);

                // Handle Co-authors
                if ($hasCoAuthors) {
                    foreach ($data['co_authors'] as $index => $co_author) {
                        $paper->authors()->create([
                            'name' => $co_author['name'],
                            'email' => $co_author['email'],
                            'designation' => $co_author['designation'],
                            'institution' => $co_author['institution'],
                            'country_id' => $co_author['country_id'],
                            'is_presenting_author' => false,
                            'author_order' => $index + 2,
                        ]);
                    }
                }
            }

            if (Cookie::get('referral_visitors') != null) {
                ReferralVisitor::where('cookie_value', Cookie::get('referral_visitors'))->first()->update(['user_id' => $user->id]);
            }

            // Sync the total amount due based on the registration type and content
            \App\Services\PricingService::updateProfileTotalDue($profile->fresh());

            DB::commit();

            // Notify if paper submitted
            if ($paper) {
                try {
                    Mail::to($user->email)->queue(new AbstractSubmitted($paper));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Mail Error (Registration Submission): ' . $e->getMessage());
                }
            }

            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));

        $settings = Setting::pluck('value', 'key');
        $isPaymentEnabled = ($settings['is_payment_enabled'] ?? 'true') == 'true';

        if ($request->action == 'save-pay' && $isPaymentEnabled) {
            $payment = new PaymentController();
            $transaction_id = rand(100, 999) . '-' . "BNC2026-" . strtotime(now());
            $payment->paymentStore($user, $transaction_id, 'onecard');

            // $sslPayment = new SslCommerzPaymentController();
            // return $sslPayment->index($request, $user, $transaction_id);

            $oneCardPayment = new OneCardPaymentController();
            return $oneCardPayment->index($request, $user, $transaction_id);
        } else {
            $message = 'Registration Complete. Please check your email to verify your account or pay later from your dashboard after login.';
            if ($isPaymentEnabled) {
                $message = 'Registration Complete. Please check your email to verify your account. Once verified, you can log in, track your paper approval, and complete your payment from your dashboard.';
            } else {
                $message = 'Thank you for registering. Please verify your email to access your account.';
            }
            return redirect('/book-ticket')->with('info', $message);
        }
    }
}
