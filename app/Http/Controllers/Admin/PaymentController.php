<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Paper;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\OneCardPaymentController;

class PaymentController extends Controller
{
    public function setPayment($user)
    {
        $randomNum= rand(100,999).'-'."BNC2026-".strtotime(now());  //substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 8);

        $payment = $this->paymentStore($user,$randomNum);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.1card.com.bd/shurjopay-dipti/pay',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'user_id' => $user->id,
                'amount' => $user->profile->pay_amount,
                'currency_code' => 'BDT',
                'cus_name' => $user->name,
                'cus_email' => $user->email,
                'cus_address' => 'Dhaka',
                'cus_city' => 'Dhaka',
                'cus_state' => 'Dhaka',
                'cus_postcode' => '1207',
                'cus_country' => 'Bangladesh',
                'cus_phone' => $user->profile->whatsapp_number ?? '01811458857',
                'response_type' => 'Json',
                'service_type' => 'paper_event',
                'success' => route('successPayment'),
                'redirect' => route('statusPayment'),
                'reff_id' => $randomNum
            ),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }


    // public function paymentStore($user,$randomNum){

    //   $paymentData =   array(
    //         'user_id' => $user->id,
    //         'amount' => $user->profile->pay_amount,
    //         'currency_code' => 'BDT',
    //         'cus_name' => $user->name,
    //         'cus_email' => $user->email,
    //         'cus_address' => 'Dhaka',
    //         'cus_city' => 'Dhaka',
    //         'cus_state' => 'Dhaka',
    //         'cus_postcode' => '1207',
    //         'cus_country' => 'Bangladesh',
    //         'cus_phone' => $user->profile->phone,
    //         'response_type' => 'Json',
    //         'service_type' => 'event',
    //         'reff_id' => $randomNum
    //     );

    //   Payment::create($paymentData);
    // }

        public function paymentStore($user,$randomNum,$getaway = 'shurjopay'){

        $paymentData =   array(
            'user_id' => $user->id,
            'amount' => $user->profile->pay_amount,
            'currency_code' => $user->profile->currency ?? 'BDT',
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_address' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1207',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $user->profile->whatsapp_number ?? '01811458857',
            'response_type' => 'Json',
            'getaway' => $getaway,
            'service_type' => 'paper_event',
            'reff_id' => $randomNum
        );

        Payment::create($paymentData);
    }






    public function myPayment(){
        return view('main.payment.my-payment');
    }
    public function payNow_old(Request $request){
        $user = User::findOrFail($request->input('user_id'));
        $this->setPayment($user);
    }

    public function payNow(Request $request){
      $user = User::findOrFail($request->input('user_id'));
        //$this->setPayment($user);
        $randomNum= rand(100,999).'-'."BNC2026-".strtotime(now());  //substr(str_shuffle
        $this->paymentStore($user,$randomNum,'onecard');

        // $sslPayment = new SslCommerzPaymentController();
        // $sslPayment->index($request,$user,$randomNum);

        $oneCardPayment = new OneCardPaymentController();
        return $oneCardPayment->index($request, $user, $randomNum);
    }

    public function payNowPapers(Request $request){
        $request->validate(['paper_ids' => 'required|array']);
        $user = User::findOrFail($request->input('user_id'));
        $paperIds = $request->input('paper_ids');

        $papers = \App\Models\Paper::whereIn('id', $paperIds)->where('user_id', $user->id)->get();
        if ($papers->count() == 0) {
            return back()->with('error', 'No authentic papers found for checkout.');
        }

        $totalAmount = 0;
        $currencyCode = 'USD';

        foreach ($papers as $paper) {
            $pricing = \App\Services\PricingService::calculatePaperCost($user->profile, $paper);
            $totalAmount += $pricing['final_price'];
            $currencyCode = $pricing['currency'];
        }

        $randomNum = rand(100,999).'-'."BNC2026-".strtotime(now());

        $paymentData = array(
            'user_id' => $user->id,
            'amount' => $totalAmount,
            'currency_code' => $currencyCode,
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_address' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1207',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $user->profile->whatsapp_number ?? '01811458857',
            'response_type' => 'Json',
            'getaway' => 'onecard',
            'service_type' => 'paper_event',
            'reff_id' => $randomNum
        );
        Payment::create($paymentData);

        $request->merge([
            'is_paper_checkout' => true,
            'calculated_amount' => $totalAmount,
            'calculated_currency' => $currencyCode,
            'checkout_paper_ids' => $paperIds
        ]);

        // $sslPayment = new SslCommerzPaymentController();
        // return $sslPayment->index($request, $user, $randomNum);

        $oneCardPayment = new OneCardPaymentController();
        return $oneCardPayment->index($request, $user, $randomNum);
    }
}
