<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Payment;

use App\Http\Controllers\SslCommerzPaymentController;

class PaymentController extends Controller
{
    public function setPayment($user)
    {
        $randomNum= rand(100,999).'-'."aicDipti-".strtotime(now());  //substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 8);

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
                'cus_phone' => $user->profile->phone,
                'response_type' => 'Json',
                'service_type' => 'event',
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
            'currency_code' => 'BDT',
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_address' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1207',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $user->profile->phone,
            'response_type' => 'Json',
            'getaway' => $getaway,
            'service_type' => 'event',
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
        $randomNum= rand(100,999).'-'."aicDipti-".strtotime(now());  //substr(str_shuffle
        $this->paymentStore($user,$randomNum,'sslcommerz');
        $sslPayment = new SslCommerzPaymentController();
        $sslPayment->index($request,$user,$randomNum);
    }


    // public function testPayment(){
    //   $user = User::findOrFail(5);
    //   $status = $this->setPayment($user);

    //   dd("test payment");
    //   if ($status==1){
    //       dd('suceess');
    //   }else if ($status==2){
    //       dd('reject');
    //   }
    //   else if ($status==3){
    //       dd('cancel');
    //   }else{
    //       dd("unknow");
    //   }
    // }
}
