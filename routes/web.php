<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SpeakersController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\VenuesController;
use App\Http\Controllers\Admin\HotelsController;
use App\Http\Controllers\Admin\GalleriesController;
use App\Http\Controllers\Admin\SponsorsController;
use App\Http\Controllers\Admin\StrategicPartnerController;
use App\Http\Controllers\Admin\FaqsController;
use App\Http\Controllers\Admin\AmenitiesController;
use App\Http\Controllers\Admin\PricesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CustomMailController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\OneCardPaymentController;

use App\Http\Controllers\Admin\BlogCategoriesController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Admin\CommentsController;
use App\Http\Controllers\Admin\UploadMediaController;
use App\Http\Middleware\CheckUniquePostView;
use App\Http\Controllers\Admin\EventActivitiesController;
use App\Http\Controllers\Admin\ReferralsController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DataBankCategoriesController;
use App\Http\Controllers\Admin\DataBanksController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\CommitteeTypeController;
use App\Http\Controllers\Admin\CommitteeController;
use App\Http\Controllers\Admin\ConferenceMemberController;
use App\Http\Controllers\Admin\PaperController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//
//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/career', [HomeController::class,'career']);
Route::get('/phpinfo', function() {
    //echo phpinfo();
});
Route::get('/clear-cache', function() {
    Artisan::call('storage:link');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return '<h1>Cache facade value cleared</h1>';
});


Route::get('call-for-papers', [HomeController::class,'callForPepper'])->name('callForPepper');


Route::get('unsubscribe/{unsubscribe_link}', [HomeController::class,'unsubscribe'])->name('data-banks.unsubscribe');
Route::get('subscribe/{unsubscribe_link}', [HomeController::class,'subscribe'])->name('data-banks.subscribe');

Route::get('admin/data-banks/send-email', [DataBanksController::class,'sendEmail'])->name('data-banks.send-email');

Route::post('admin/data-banks/send-email', [DataBanksController::class,'dataBankSendEmail'])->name('data-banks.dataBankSendEmail');


Route::get('/generate_ids', [HomeController::class, 'generateIds'])->name('generateIds');
Route::get('/generate_ids/{id}', [HomeController::class, 'generateIds'])->name('generateIds');

// SSLCOMMERZ Start
// Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
// Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

// Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
// Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

// Route::post('/success', [SslCommerzPaymentController::class, 'success']);
// Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
// Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

// Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END

// OneCard Start
Route::post('/onecard/pay', [OneCardPaymentController::class, 'index'])->name('onecard.pay');
Route::post('/onecard/success', [OneCardPaymentController::class, 'success'])->name('onecard.success');
Route::get('/onecard/redirect', [OneCardPaymentController::class, 'redirect'])->name('onecard.redirect');
Route::post('/admin/onecard/verify', [OneCardPaymentController::class, 'verifyPayment'])->name('onecard.verify');
// OneCard END

// success
Route::get('/success/{ord_token}', [HomeController::class, 'success'])->name('success');
Route::get('/success/', [HomeController::class, 'success'])->name('success');
Route::get('/cancel/', [OneCardPaymentController::class, 'cancel'])->name('cancel');
Route::get('/fail/', [OneCardPaymentController::class, 'fail'])->name('fail');

//end ssl




Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/book-ticket', [HomeController::class,'bookTicket'])->name('book-ticket');
Route::get('/book-ticket/{title}', [HomeController::class,'bookTicket'])->name('book-ticket-referral');
Route::get('/check-referral-coupon', [HomeController::class,'checkReferralCoupon'])->name('checkReferralCoupon');

Route::get('/privacy-policy', [HomeController::class,'privacyPolicy'])->name('privacy-policy');
Route::get('/event/{id}/{slug}', [HomeController::class, 'singleEvent'])->name('singleEvent');
Route::get('speaker/{slug}', [HomeController::class, 'view'])->name('speaker');
Route::redirect('/home', '/admin');
Route::get('schedules/{id}/{title}', [HomeController::class,'scheduleDetails'])->name('scheduleDetails');


Route::get('/blogs', [HomeController::class, 'blogs'])->name('blogs');
Route::get('/blogs/tag/{id}/{slug}', [HomeController::class, 'tags'])->name('tags');
Route::get('/blogs/{id}/{slug}', [HomeController::class, 'blogsCategory'])->name('blogsCategory');
Route::post('/new-comment', [CommentsController::class, 'newComments'])->name('newComments');
Route::post('/likes', [CommentsController::class, 'like'])->name('like');
// routes/web.php

Route::post('/submit-reply', [CommentsController::class,'submitReply'])->name('submitReply');

    Route::get('/blog-details/{id}/{slug}', [HomeController::class, 'blogDetails'])->name('blogDetails')->middleware(CheckUniquePostView::class);;


Auth::routes(['register' => false,'verify'=>true,]);


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth','verified']], function () {

    Route::post('feedback', [FeedbackController::class,'store'])->name('feedback.store');


    Route::get('/', [DashboardController::class, 'index'])->name('home');
    // Permissions
    Route::delete('permissions/destroy', [PermissionsController::class,'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionsController::class);
    // Roles

    Route::delete('roles/destroy', [RolesController::class,'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);

    // Users
    Route::delete('users/destroy', [UsersController::class,'massDestroy'])->name('users.massDestroy');
    Route::resource('users', UsersController::class);

    // Settings
    Route::delete('settings/destroy', [SettingsController::class,'massDestroy'])->name('settings.massDestroy');
    Route::resource('settings', SettingsController::class);

    // Speakers

    Route::delete('speakers/destroy', [SpeakersController::class,'massDestroy'])->name('speakers.massDestroy');
    Route::post('speakers/media', [SpeakersController::class,'storeMedia'])->name('speakers.storeMedia');
    Route::resource('speakers', SpeakersController::class);


    // Schedules
    Route::delete('schedules/destroy', [ScheduleController::class,'massDestroy'])->name('schedules.massDestroy');
    Route::resource('schedules', ScheduleController::class);

    // Venues
    Route::delete('venues/destroy', [VenuesController::class,'massDestroy'])->name('venues.massDestroy');
    Route::post('venues/media', [VenuesController::class,'storeMedia'])->name('venues.storeMedia');
    Route::resource('venues', VenuesController::class);

    // Hotels
    Route::delete('hotels/destroy', [HotelsController::class,'massDestroy'])->name('hotels.massDestroy');
    Route::post('hotels/media', [HotelsController::class,'storeMedia'])->name('hotels.storeMedia');
    Route::resource('hotels', HotelsController::class);

    // Galleries

    Route::delete('galleries/destroy', [GalleriesController::class,'massDestroy'])->name('galleries.massDestroy');
    Route::post('galleries/media', [GalleriesController::class,'storeMedia'])->name('galleries.storeMedia');
    Route::resource('galleries', GalleriesController::class);

    // Sponsors
    Route::delete('sponsors/destroy', [SponsorsController::class,'massDestroy'])->name('sponsors.massDestroy');
    Route::post('sponsors/media', [SponsorsController::class,'storeMedia'])->name('sponsors.storeMedia');
    Route::resource('sponsors', SponsorsController::class);

    // Strategic Partner
    Route::delete('strategics/destroy', [StrategicPartnerController::class,'massDestroy'])->name('strategics.massDestroy');
    Route::post('strategics/media', [StrategicPartnerController::class,'storeMedia'])->name('strategics.storeMedia');
    Route::resource('strategics', StrategicPartnerController::class);

    // Faqs

    Route::delete('faqs/destroy', [FaqsController::class,'massDestroy'])->name('faqs.massDestroy');
    Route::resource('faqs', FaqsController::class);

    // Amenities
    Route::delete('amenities/destroy', [AmenitiesController::class,'massDestroy'])->name('amenities.massDestroy');
    Route::resource('amenities', AmenitiesController::class);

    // Prices
    Route::delete('prices/destroy', [PricesController::class,'massDestroy'])->name('prices.massDestroy');
    Route::resource('prices', PricesController::class);

    // Events
    Route::delete('events/destroy', [EventsController::class,'massDestroy'])->name('events.massDestroy');
    Route::post('events/media', [EventsController::class,'storeMedia'])->name('events.storeMedia');
    Route::resource('events', EventsController::class);

    //Coupon
    Route::delete('coupons/destroy', [CouponController::class,'massDestroy'])->name('coupons.massDestroy');
    Route::post('coupons/media', [CouponController::class,'storeMedia'])->name('coupons.storeMedia');
    Route::resource('coupon',CouponController::class);

    //domain
    Route::resource('domain',DomainController::class);
    //Custom Mail
    Route::resource('custom-mail',CustomMailController::class);
    Route::post('custom-mail/media', [CustomMailController::class,'storeMedia'])->name('custom-mail.storeMedia');
    Route::post('custom-mail/ckmedia', [CustomMailController::class,'storeCKEditorImages'])->name('custom-mail.storeCKEditorImages');

    //blog related route
    // Blog Categories
    Route::delete('blog-categories/destroy', [BlogCategoriesController::class,'massDestroy'])->name('blog-categories.massDestroy');
    Route::post('blog-categories/media', [BlogCategoriesController::class,'storeMedia'])->name('blog-categories.storeMedia');
    Route::post('blog-categories/ckmedia', [BlogCategoriesController::class,'storeCKEditorImages'])->name('blog-categories.storeCKEditorImages');
    Route::resource('blog-categories', BlogCategoriesController::class);

    // Tags
    Route::delete('tags/destroy', [TagsController::class,'massDestroy'])->name('tags.massDestroy');
    Route::resource('tags', TagsController::class);

    // Posts
    Route::delete('posts/destroy', [PostsController::class,'massDestroy'])->name('posts.massDestroy');
    Route::post('posts/media', [PostsController::class,'storeMedia'])->name('posts.storeMedia');
    Route::post('posts/ckmedia', [PostsController::class,'storeCKEditorImages'])->name('posts.storeCKEditorImages');
    Route::resource('posts', PostsController::class);

    // Comments
    Route::delete('comments/destroy', [CommentsController::class,'massDestroy'])->name('comments.massDestroy');
    Route::resource('comments', CommentsController::class);

    // Upload Media
    Route::delete('upload-media/destroy',  [UploadMediaController::class,'massDestroy'])->name('upload-media.massDestroy');
    Route::post('upload-media/media',  [UploadMediaController::class,'storeMedia'])->name('upload-media.storeMedia');
    Route::post('upload-media/ckmedia',  [UploadMediaController::class,'storeCKEditorImages'])->name('upload-media.storeCKEditorImages');
    Route::resource('upload-media', UploadMediaController::class);


    // Event Activities
    Route::delete('event-activities/destroy', [EventActivitiesController::class,'massDestroy'])->name('event-activities.massDestroy');
    Route::post('event-activities/media',  [EventActivitiesController::class,'storeMedia'])->name('event-activities.storeMedia');
    Route::post('event-activities/ckmedia',  [EventActivitiesController::class,'storeCKEditorImages'])->name('event-activities.storeCKEditorImages');
    Route::resource('event-activities',  EventActivitiesController::class);


    // Referrals
    Route::delete('referrals/destroy', [ReferralsController::class,'massDestroy'])->name('referrals.massDestroy');
    Route::post('referrals/media', [ReferralsController::class,'storeMedia'])->name('referrals.storeMedia');
    Route::post('referrals/ckmedia', [ReferralsController::class,'storeCKEditorImages'])->name('referrals.storeCKEditorImages');
    Route::resource('referrals', ReferralsController::class);
    //Attendances function
    Route::resource('attendances',AttendanceController::class);
    Route::post('attendance-present', [AttendanceController::class,'updateAttendance'])->name('attendance-present');
    Route::get('download-certificate/{id}', [AttendanceController::class,'downloadCertificate'])->name('downloadCertificate');
 Route::get('event-attendance', [AttendanceController::class,'eventAttendance'])->name('eventAttendance');
    Route::post('eventAttendance', [AttendanceController::class,'eventAttendanceTotal'])->name('eventAttendanceTotal');

    // Data Bank Categories
    Route::delete('data-bank-categories/destroy', [DataBankCategoriesController::class,'massDestroy'])->name('data-bank-categories.massDestroy');
    Route::resource('data-bank-categories', DataBankCategoriesController::class);
    // Data Banks
    Route::delete('data-banks/destroy', [DataBanksController::class,'massDestroy'])->name('data-banks.massDestroy');
    Route::post('data-banks/parse-csv-import', [DataBanksController::class,'parseCsvImport'])->name('data-banks.parseCsvImport');
    Route::post('data-banks/process-csv-import', [DataBanksController::class,'processCsvImport'])->name('data-banks.processCsvImport');

    Route::resource('data-banks', DataBanksController::class);

    // Committee Types
    Route::delete('committee-types/destroy', [CommitteeTypeController::class, 'massDestroy'])->name('committee-types.massDestroy');
    Route::resource('committee-types', CommitteeTypeController::class);

    // Committees
    Route::delete('committees/destroy', [CommitteeController::class, 'massDestroy'])->name('committees.massDestroy');
    Route::resource('committees', CommitteeController::class);

    // Committee  Members
    Route::delete('conference-members/destroy', [ConferenceMemberController::class, 'massDestroy'])->name('conference-members.massDestroy');
    Route::resource('conference-members', ConferenceMemberController::class);

    // Papers Admin Actions
    Route::post('papers/{paper}/review', [PaperController::class, 'review'])->name('papers.review');
    Route::post('papers/{paper}/approve', [PaperController::class, 'approve'])->name('papers.approve');
    Route::post('papers/{paper}/reject', [PaperController::class, 'reject'])->name('papers.reject');
});

    // Paper Submission (Post-registration)
   // Route::get('papers/submit', [App\Http\Controllers\Admin\PaperController::class, 'create'])->name('papers.submit');
  //  Route::post('papers/submit', [App\Http\Controllers\Admin\PaperController::class, 'store'])->name('papers.store');
Route::resource('papers', PaperController::class);




//Route::get('book-ticket',[ProfileController::class,'create'])->name('book-ticket');
// Participant Routes (Authenticated & Verified)
Route::group(['middleware' => ['auth', 'verified']], function () {
    // Profile
    Route::get('show/profile', [ProfileController::class, 'index'])->name('show-profile');
    Route::post('save/profile', [ProfileController::class, 'store'])->name('save-profile');
    Route::get('edit/profile/{id}', [ProfileController::class, 'edit'])->name('edit-profile');
    Route::post('update/profile', [ProfileController::class, 'update'])->name('update-profile');
    Route::post('validate-coupon', [ProfileController::class, 'validateCoupon'])->name('validateCoupon');

    // Paper Management
    Route::get('papers', [App\Http\Controllers\Admin\PaperController::class, 'index'])->name('papers.index');
    Route::get('papers/submit', [App\Http\Controllers\Admin\PaperController::class, 'create'])->name('papers.submit');
    Route::post('papers/submit', [App\Http\Controllers\Admin\PaperController::class, 'store'])->name('papers.store');
    Route::get('papers/{paper}', [App\Http\Controllers\Admin\PaperController::class, 'show'])->name('papers.show');
    Route::get('papers/{paper}/pricing', [App\Http\Controllers\Admin\PaperController::class, 'getPaperPricing'])->name('papers.pricing');

    // Payments
    Route::get('set/payment/{data}', [PaymentController::class, 'setPayment'])->name('setPayment');
    Route::get('my-payment', [PaymentController::class, 'myPayment'])->name('myPayment');
    Route::post('pay-now', [PaymentController::class, 'payNow'])->name('payNow');
    Route::post('pay-now-papers', [PaymentController::class, 'payNowPapers'])->name('payNowPapers');
    Route::get('status/payment', [ProfileController::class, 'statusPayment'])->name('statusPayment');
    Route::get('payment/complete', [ProfileController::class, 'paymentComplete'])->name('payment-complete');
    Route::get('payment/not-complete', [ProfileController::class, 'paymentNotComplete'])->name('payment-not-complete');
});

Route::post('send-mail', [MailController::class, 'sendMail'])->name('send-message');


Route::get('test-payment',[PaymentController::class,'testPayment'])->name('testPayment');


// Route::get('book-ticket2',[ProfileController::class,'createProfile'])->name('book-ticket-test');
// Route::get('show/profile2',[ProfileController::class,'showProfile'])->name('show-profile-test');
// Route::post('pay-now2',[PaymentController::class,'payNow2'])->name('payNow-test');
// Route::post('register2',[RegisterController::class,'register2'])->name('register2');

Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'redirectHome'])->name('home_redirect');



