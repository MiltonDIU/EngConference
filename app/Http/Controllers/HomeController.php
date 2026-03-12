<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\BlogCategory;
use App\Models\DataBank;
use App\Models\Event;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Referral;
use App\Models\ReferralVisitor;
use App\Models\Setting;
use App\Models\Speaker;
use App\Models\Schedule;
use App\Models\Tag;
use App\Models\Venue;
use App\Models\Comment;
use App\Models\Hotel;
use App\Models\Gallery;
use App\Models\Sponsor;
use App\Models\StrategicPartner;
use App\Models\Faq;
use App\Models\Price;
use App\Models\Amenity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckUniquePostView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
class HomeController extends Controller
{
protected $noReferral = array(
    'developers.google.com',
    'facebookexternalhit',
    'WhatsApp',
    'LinkedInBot',
);

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $settings = Setting::pluck('value', 'key');
        $speakers = Speaker::where('show_home',1)->orderBy('serial','asc')->get();
        $schedules = Schedule::with('speaker')
            ->orderBy('day_number', 'asc')
            ->orderBy('start_time', 'asc')
            ->where('is_active','1')
            ->get()
            ->groupBy('day_number');

        $venues = Venue::all();
        $hotels = Hotel::all();
        $galleries = Gallery::all();
        $sponsors = Sponsor::orderBy('serial','asc')->get();
        $strategics = StrategicPartner::orderBy('serial','asc')->get();
        $faqs = Faq::all();
        $prices = Price::with('amenities')->get();
        $amenities = Amenity::with('prices')->get();
        if (Auth::user()) {
            $profile = Profile::where('user_id', $user->id)->first();
            return view('main.home', compact('settings', 'speakers', 'schedules', 'venues', 'hotels', 'galleries', 'sponsors', 'strategics', 'faqs', 'prices', 'amenities','profile'));
        }else {
            return view('main.home', compact('settings', 'speakers', 'schedules', 'venues', 'hotels', 'galleries', 'sponsors', 'strategics', 'faqs', 'prices', 'amenities'));
        }
    }

    public function singleEvent($id,$slug)
    {
        $settings = Setting::pluck('value', 'key');
        $event = Event::find($id);
//        $speakers = Speaker::all();
        $speakers = $event->speakers;
        $schedules = Schedule::with('speaker')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('day_number');
//        $venues = Venue::all();
        $venues = $event->venues;
//        $hotels = Hotel::all();
        $hotels = $event->hotels;
        $galleries = Gallery::all();
//        $sponsors = Sponsor::all();
        $sponsors = $event->sponsors;
//        $faqs = Faq::all();
        $faqs = $event->faqs;
//        $prices = Price::with('amenities')->get();
        $prices = $event->prices;
        $amenities = Amenity::with('prices')->get();
        return view('theme2.home', compact('event','settings', 'speakers', 'schedules', 'venues', 'hotels', 'galleries', 'sponsors', 'faqs', 'prices', 'amenities'));


    }

    public function view($slug)
    {
        $settings = Setting::pluck('value', 'key');
        $speaker = Speaker::where('slug',$slug)->first();
        return view('main.speaker', compact('settings', 'speaker'));
    }

    public function privacyPolicy()
    {
        return view('main.privacy');
    }
    public function bookTicket(Request $request,$referral=null){
        if (Auth::user()) {
            return redirect(route('admin.home'));
        } else {

                $this->setCookie($referral,$request);
                $schedules = Schedule::with(['speaker', 'users' => function ($query) {
                    $query->whereHas('profile', function ($subQuery) {
                        $subQuery->where('payment_status', '1');
                    });
                }])
                ->where('is_workshop', '1')
                ->where('is_active', '1')
                    ->orWhere('event_session', '1')
                ->orderBy('day_number', 'asc')->orderBy('start_time', 'asc')
                ->get()
                ->filter(function ($schedule) {
                    return $schedule->total_seat > $schedule->users->count();
                })
                ->groupBy('day_number');
            $settings = Setting::pluck('value', 'key');
            $aminities = Amenity::orderBy('id','desc')->get();
            return view('main.registration',compact('settings','schedules','referral','aminities'));
        }

    }




    public function setCookie($referral,$request){

        if ($referral != null) {
            // Set the cookie name and value
            $cookieName = 'referral_visitors';
            $cookieValue = time(); // Store the current timestamp
            $minutes = 1000; //7 days
            $ip = $request->ip();
            $referralData = Referral::where('identification', $referral)->first();
            $userAgent = $request->headers->get('user-agent');
            $data['referral_identification']= $referral;
            $data['ip_address']= $request->ip();
            $data['cookie_value']= $cookieValue;
            $data['cookie_name']= $cookieName;
            $data['minutes']= $minutes;
            $data['user_agent']= $userAgent;
            if ($referralData!=null) {
                if (Cookie::get($cookieName)===null) {
                    $this->checkUserAgent($userAgent,$data,$cookieName,$cookieValue,$minutes);
                }else{
                    $findCookieValue = ReferralVisitor::where('cookie_value', Cookie::get($cookieName))->first();
                    if ($findCookieValue==null ){
                        $this->checkUserAgent($userAgent,$data,$cookieName,$cookieValue,$minutes);
                    }else{
                        if (($request->headers->get('user-agent')!=$findCookieValue->user_agent) || ($request->ip()!=$findCookieValue->ip_address)){
                            $this->checkUserAgent($userAgent,$data,$cookieName,$cookieValue,$minutes);
                        }
                    }
                }

            }
        }
    }

public function checkUserAgent($userAgent,$data,$cookieName,$cookieValue,$minutes){
    if ($userAgent) {
        $matchFound = false;
        foreach ($this->noReferral as $blockedValue) {
            if (stripos($userAgent, $blockedValue) !== false) {
                // If a match is found, set $matchFound to true and break out of the loop
                $matchFound = true;
                break;
            }
        }
        if (!$matchFound) {
            Cookie::queue($cookieName, $cookieValue, $minutes);
            ReferralVisitor::create($data);
        }
    }
}




    public function blogs(){
        $settings = Setting::pluck('value', 'key');
        $blogs = Post::where('is_active','1')->orderBy('created_at','desc')->get();
        $populers = Post::where('is_active', '1')
            ->orderBy('views', 'desc')
            ->get()->take(3);
        $blogCategories = BlogCategory::where('is_active', '1')->orderBy('id', 'asc')->get();
        return view('main.blogs',compact('blogs','populers','blogCategories','settings'));

    }
    public function blogDetails($id){
        $settings = Setting::pluck('value', 'key');

        $this->middleware(CheckUniquePostView::class);

        $blog = Post::where('is_active','1')->where('id',$id)->first();
//        $blogs = Post::where('is_active', '1')
//            ->where('blog_category_id', $blog->blog_category_id)
//            ->whereNotIn('id', [$blog->id])
//            ->orderBy('created_at', 'desc')
//            ->get();
        $populers =  Post::where('is_active', '1')
            ->whereNotIn('id', [$blog->id])
            ->orderBy('views', 'desc')
            ->get()->take(3);
        $comments = Comment::where('post_id',$id)->where('is_active','1')->where('parent_id',null)->orderBy('created_at','desc')->get();

        $blogCategories = BlogCategory::where('is_active', '1')->orderBy('id', 'asc')->get();
        if (!$blog){
            return redirect(route('blogs'));
        }
        return view('main.blogs-details',compact('blog','populers','blogCategories','settings','comments'));
    }
public function blogsCategory($id,$slug){
    $settings = Setting::pluck('value', 'key');
    $blogs = Post::where('is_active','1')->where('blog_category_id',$id)->orderBy('created_at','desc')->get();
    $populers = Post::where('is_active', '1')
        ->orderBy('views', 'desc')
        ->get()->take(3);
    $blogCategories = BlogCategory::where('is_active', '1')->orderBy('id', 'asc')->get();
    $blogCategorie = BlogCategory::where('is_active', '1')->find($id);
    $subtitle = $blogCategorie->title;

    return view('main.blogs',compact('blogs','populers','blogCategories','settings','subtitle'));
}

    public function tags($id,$slug){

        $settings = Setting::pluck('value', 'key');
        $tag = Tag::findOrFail($id);
        $blogs = $tag->posts;
        $populers = Post::where('is_active', '1')
            ->orderBy('views', 'desc')
            ->get()->take(3);
        $blogCategories = BlogCategory::where('is_active', '1')->orderBy('id', 'asc')->get();
        $blogCategorie = BlogCategory::where('is_active', '1')->find($id);
        $subtitle = $tag->name;

        return view('main.blogs',compact('blogs','populers','blogCategories','settings','subtitle'));
    }

    public function cancel(){
        return view('theme2.payment.cancel');
    }
    public function fail(){
        return view('theme2.payment.fail');
    }
    public function success(){
            $profile = Profile::where('user_id',Auth::id())->first();
        if ($profile != null){
            $newId = $this->uniqueIdGenerate();
            $profile->identity_no = $newId;
            $profile->save();
        }
        return view('theme2.payment.success');
    }

    public function generateIds($id=null){

        if ($id!=null){
            $profile = Profile::where('id',$id)->first();
        }else{
            $profile = Profile::where('user_id',Auth::id())->first();
        }
        $newId = $this->uniqueIdGenerate();

        $profile->identity_no = $newId;
        $result = $profile->save();
        return redirect()->back();
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
public function scheduleDetails($id,$title){

  $schedule = Schedule::where('id',$id)->first();
  $schedule =
    $settings = Setting::pluck('value', 'key');
    $populers = Post::where('is_active', '1')
        ->orderBy('views', 'desc')
        ->get()->take(3);
    $blogCategories = BlogCategory::where('is_active', '1')->orderBy('id', 'asc')->get();
    return view('main.schedule-details',compact('schedule','populers','blogCategories','settings'));

}

public function checkReferralCoupon(Request $request){
        // Extract the last segment (parameter) from the URL
        $referralId = $request->referral_id;
        // Retrieve the referral based on the extracted ID
        $referral = Referral::where('identification', $referralId)->where('is_active','1')->first();
        if ($referral) {
            // Assuming there's a relationship between Referral and Coupon models
            $coupon = $referral->coupon;
            if ($coupon) {
                // Return the coupon ID in the response
                return response()->json(['coupon_id' => $coupon->title]);
            }
        }
        // If referral or coupon not found, return an empty response or appropriate error
        return response()->json(['error' => 'Referral or Coupon not found'], 404);


}
public function unsubscribe($unsubscribe){
        $dataBank = DataBank::where('unsubscribe_link',$unsubscribe)->first();
$msg = "Unsubscribed";
$link = true;
        if ($dataBank!=null){
            if ($dataBank->is_subscribe==0){
                $message =  "You have already unsubscribed. This means you are opting out of our email list where we constantly share dynamic and valuable opportunities.";
                return view('main.unsubscribe',compact('message','dataBank','msg','link'));
            }else{
               $data['is_subscribe'] = '0';
                $dataBank->update($data);
                $message =  "You have unsubscribed. This means you are opting out of our email list where we constantly share dynamic and valuable opportunities.";
                return view('main.unsubscribe',compact('message','dataBank','msg','link'));
            }
        }else{
            return redirect(url('/'));
        }
    }

    public function subscribe($unsubscribe){
        $dataBank = DataBank::where('unsubscribe_link',$unsubscribe)->first();
        $msg = "Subscribed";
        $link = false;
        if ($dataBank!=null){
            if ($dataBank->is_subscribe==1){
                $message =  "Thank you! You have already subscribed! You will receive exciting offers and opportunities from us straight away.";
                return view('main.unsubscribe',compact('message','dataBank','msg','link'));
            }else{
                $data['is_subscribe'] = '1';
                $dataBank->update($data);
                $message =  "Thank you! You have subscribed! You will receive exciting offers and opportunities from us straight away.";
                return view('main.unsubscribe',compact('message','dataBank','msg','link'));
            }
        }else{
            return redirect(url('/'));
        }
    }

}
