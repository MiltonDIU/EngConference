<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class PaymentApiController extends Controller
{
    public function successPayment(Request $request){
        $reff_id = $request->reff_id;
        // Log::info('payment reff_id  --'.$reff_id);
        $validation_result = $this->checkPayment($reff_id);

        $payment = Payment::where('reff_id',$reff_id)->first();
        if ($validation_result['data']['status']){
            if($validation_result['data']['status']=="VALIDATED"){
                $data['message']= $validation_result;
                $data['status'] = 1;
                $profile = $payment->user->profile;
                $payment->update($data);
                $profileData['payment_status']='1';
                $profile->update($profileData);

            }else{
                $data['message']= $validation_result;
                $data['status'] = 2;
                $profile = $payment->user->profile;
                $payment->update($data);
                $profileData['payment_status']='2';
                $profile->update($profileData);
            }
        }else{
            $data['message']= $validation_result;
            $data['status'] = 2;
            $profile = $payment->user->profile;
            $payment->update($data);
            $profileData['payment_status']='2';
            $profile->update($profileData);
        }
    }

    public function statusPayment(Request $request){
        $settings = Setting::pluck('value', 'key');
        $message = 'Success';
        return view('main.payment.my-payment',compact('settings'))->with('message',$message);
    }
    
    // public function statusPayment(Request $request)
    // {
    //     $settings = Setting::pluck('value', 'key');
    //     $message = 'Success';
    
    //     $viewData = [
    //         'settings' => $settings,
    //         'message' => $message
    //     ];
    
    //     $view = view('main.payment.my-payment', $viewData)->render();
    
    //     return Response::json([
    //         'view' => $view
    //     ]);
    // }

    public function checkPayment($reff_id){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.1card.com.bd/shurjopay-dipti/validationserverapi',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'reff_id' => $reff_id,
                'token' => 'ceb263b97edc55698ab6fcf755ebcc1d'
            ),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $result = curl_exec($curl);

        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($curl));
        }
        curl_close($curl);
        $api_result = json_decode($result,true);
        if($api_result['message']){
             return $api_result;
        }else{
            return false;
        }

    }
}
