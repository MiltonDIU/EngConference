<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Profile;
use App\Models\Paper;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationConfirmed;
use Illuminate\Support\Facades\Log;
class SslCommerzPaymentController extends Controller
{

    public function exampleEasyCheckout()
    {
        return view('exampleEasycheckout');
    }

    public function exampleHostedCheckout()
    {
        return view('exampleHosted');
    }

    public function index(Request $request,$user,$transaction_id)

    {

        //dd($request->all());




        # Here you have to receive all the order data to initate the payment.
        # Let's say, your oder transaction informations are saving in a table called "orders"
        # In "orders" table, order unique identity is "transaction_id". "status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $post_data = array();
        
        if ($request->input('is_paper_checkout')) {
            // Paper checkout flow
            $post_data['total_amount'] = $request->input('calculated_amount');
            $post_data['currency'] = $request->input('calculated_currency');
            $paper_ids_json = json_encode($request->input('checkout_paper_ids'));
        } else {
            // Standard profile checkout flow
            $paper_ids_json = null;
            $profile = $user->profile;
            if ($request->input('special_discount') && $profile) {
                $discountedAmount = $profile->pay_amount * 0.20; // Calculate the discount amount (20% of the original amount)
                $newTotalAmount = $profile->pay_amount - $discountedAmount; // Calculate the new total amount after applying the discount
                $post_data['total_amount'] = $newTotalAmount; # You can not pay less than 10
                
                // Note: Only saving if the column exists to avoid further SQL errors
                // If the discount column is missing, this will be skipped safely
                try {
                    $profile->discount = $discountedAmount;
                    $profile->save();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not save discount to profile: " . $e->getMessage());
                }
            } else {
                $post_data['total_amount'] = $profile->pay_amount ?? 0;
                if ($profile) {
                    try {
                        $profile->discount = 0;
                        $profile->save();
                    } catch (\Exception $e) {
                        // Ignore if discount column doesn't exist
                    }
                }
            }
            $post_data['currency'] = $profile->currency ?? "BDT";
        }
        
        $post_data['tran_id'] = $transaction_id;//uniqid(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->name;
        $post_data['cus_email'] = $user->email;
        $post_data['user_id'] = $user->id;
        $post_data['cus_add1'] = 'Dhaka';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $user->profile->whatsapp_number ?? "01811458857";
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";

        #Before  going to initiate the payment order status need to insert or update as Pending.
        $update_product = DB::table('orders')
            ->where('transaction_id', $post_data['tran_id'])
            ->updateOrInsert([
                'name' => $post_data['cus_name'],
                'user_id' => $post_data['user_id'],
                'email' => $post_data['cus_email'],
                'phone' => $post_data['cus_phone'],
                'amount' => $post_data['total_amount'],
                'status' => 'Pending',
                'address' => $post_data['cus_add1'],
                'transaction_id' => $post_data['tran_id'],
                'currency' => $post_data['currency'],
                'paper_ids' => $paper_ids_json
            ]);

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }

    public function payViaAjax(Request $request)
    {

        # Here you have to receive all the order data to initate the payment.
        # Lets your oder trnsaction informations are saving in a table called "orders"
        # In orders table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $post_data = array();
        $post_data['total_amount'] = '10'; # You cant not pay less than 10
        $post_data['currency'] = $request->input('currency', "BDT");
        $post_data['tran_id'] = uniqid(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = 'Customer Name';
        $post_data['cus_email'] = 'customer@mail.com';
        $post_data['cus_add1'] = 'Customer Address';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = '8801XXXXXXXXX';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";


        #Before  going to initiate the payment order status need to update as Pending.
        $update_product = DB::table('orders')
            ->where('transaction_id', $post_data['tran_id'])
            ->updateOrInsert([
                'name' => $post_data['cus_name'],
                'email' => $post_data['cus_email'],
                'phone' => $post_data['cus_phone'],
                'amount' => $post_data['total_amount'],
                'status' => 'Pending',
                'address' => $post_data['cus_add1'],
                'transaction_id' => $post_data['tran_id'],
                'currency' => $post_data['currency']
            ]);

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }

    public function success(Request $request)
    {
       // echo "Transaction is Successful";
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount', 'paper_ids')->first();

        if ($order_details->status == 'Pending') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Complete.
                Here you can also sent sms or email for successfull transaction to customer
                */
                $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Processing']);




                DB::table('payments')
                    ->where('reff_id', $tran_id)
                    ->update(['status' => 1]);
                $orderPayment = Payment::where('reff_id', $tran_id)->first();
                $profile = $orderPayment->user->profile;
                
                if ($order_details->paper_ids) {
                    $pIds = json_decode($order_details->paper_ids, true);
                    if (is_array($pIds) && count($pIds) > 0) {
                        \App\Models\Paper::whereIn('id', $pIds)->update([
                            'payment_status' => '1',
                            'pay_amount' => $amount / count($pIds),
                            'currency' => $currency
                        ]);
                    }
                } else {
                    $newId = \App\Services\IdGeneratorService::generateRegistrationId();
                    DB::table('profiles')
                        ->where('id', $profile->id)
                        ->update([
                            'payment_status' => '1',
                            'registration_id' => $newId
                        ]);
                    $profile->refresh();
                }


                // Send Registration Confirmation Email
                try {
                    $mailData = [
                        'first_name' => $profile->first_name,
                        'last_name' => $profile->last_name,
                        'name' => $orderPayment->user->name,
                        'registration_id' => $profile->registration_id,
                        'category' => 'Presenter', // Default category
                        'mode' => $profile->mode_of_participation ?? 'Onsite',
                        'amount' => $amount,
                        'currency' => $currency,
                        'transaction_id' => $tran_id,
                    ];
                    Mail::to($orderPayment->user->email)->queue(new RegistrationConfirmed($mailData));
                } catch (\Exception $e) {
                    Log::error('Mail Sending Error (Payment Success): ' . $e->getMessage());
                }

                //echo "<br >Transaction is successfully Completed";
                return redirect()->route('success')->with('message',' Transaction is successfully Completed');
            }
        } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
             */
           // echo "Transaction is successfully Completed";
            return redirect()->route('success')->with('message',' Transaction is successfully Completed');
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            //echo "Invalid Transaction";
            return redirect()->route('fail')->with('message',' Transaction is Invalid');
        }


    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount', 'paper_ids')->first();

        if ($order_details->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Failed']);

            DB::table('payments')
                ->where('reff_id', $tran_id)
                ->update(['status' => 2]);
            $orderPayment = Payment::where('reff_id', $tran_id)->first();
            $profile = $orderPayment->user->profile;
            
            if ($order_details->paper_ids) {
                // Ignore paper updates on fail
            } else {
                DB::table('profiles')
                    ->where('id', $profile->id)
                    ->update(['payment_status' => '2']);
            }





            return redirect()->route('fail')->with('message',' Transaction is Failed');

           // echo "Transaction is Falied";
        } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {
           // echo "Transaction is already Successful";
            return redirect()->route('success')->with('message',' Transaction is already Successful');
        } else {
           // echo "Transaction is Invalid";
            return redirect()->route('fail')->with('message',' Transaction is Invalid');
        }

    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount', 'paper_ids')->first();

        if ($order_details->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Canceled']);
            DB::table('payments')
                ->where('reff_id', $tran_id)
                ->update(['status' => 3]);
            $orderPayment = Payment::where('reff_id', $tran_id)->first();
            $profile = $orderPayment->user->profile;


//            DB::table('profiles')
//                ->where('id', $profile->id)
//                ->update(['payment_status' => '3']);

           // $newId = $this->uniqueIdGenerate();

//            DB::table('profiles')
//                ->where('id', $profile->id)
//                ->update([
//                    'payment_status' => '3',
//                    'identity_no' => $newId
//                ]);

            //echo "Transaction is Cancel";
            return redirect()->route('cancel')->with('message',' Your payment has been canceled');
        } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {
            //echo "Transaction is already Successful";
            return redirect()->route('success')->with('message',' Transaction is already Successful');
        } else {
           // echo "Transaction is Invalid";
            return redirect()->route('fail')->with('message',' Transaction is Invalid');
        }


    }

    public function ipn(Request $request)
    {

        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->select('transaction_id', 'status', 'currency', 'amount', 'paper_ids')->first();

            if ($order_details->status == 'Pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $order_details->amount, $order_details->currency);
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Processing']);

                DB::table('payments')
                    ->where('reff_id', $tran_id)
                    ->update(['status' => 1]);
                $orderPayment = Payment::where('reff_id', $tran_id)->first();
                $profile = $orderPayment->user->profile;
                
                if ($order_details->paper_ids) {
                    $pIds = json_decode($order_details->paper_ids, true);
                    if (is_array($pIds) && count($pIds) > 0) {
                        \App\Models\Paper::whereIn('id', $pIds)->update([
                            'payment_status' => '1',
                            'pay_amount' => $order_details->amount / count($pIds),
                            'currency' => $order_details->currency
                        ]);
                    }
                } else {
                    DB::table('profiles')
                        ->where('id', $profile->id)
                        ->update(['payment_status' => '1']);
                }

                // Send Registration Confirmation Email
                try {
                    $mailData = [
                        'first_name' => $profile->first_name,
                        'last_name' => $profile->last_name,
                        'name' => $orderPayment->user->name,
                        'registration_id' => $profile->registration_id,
                        'category' => 'Presenter',
                        'mode' => $profile->mode_of_participation ?? 'Onsite',
                        'amount' => $order_details->amount,
                        'currency' => $order_details->currency,
                        'transaction_id' => $tran_id,
                    ];
                    Mail::to($orderPayment->user->email)->queue(new RegistrationConfirmed($mailData));
                } catch (\Exception $e) {
                    Log::error('Mail Sending Error (Payment IPN): ' . $e->getMessage());
                }

                    return redirect()->route('success')->with('message',' Transaction is successfully Completed');
                  //  echo "Transaction is successfully Completed";
                }
            } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {

                #That means Order status already updated. No need to udate database.

              //  echo "Transaction is already successfully Completed";
                return redirect()->route('success')->with('message',' Transaction is already successfully Completed');
            } else {
                #That means something wrong happened. You can redirect customer to your product page.
                return redirect()->route('fail')->with('message',' Invalid Transaction');
              //  echo "Invalid Transaction";
            }
        } else {
            return redirect()->route('fail')->with('message',' Invalid Data');
           // echo "Invalid Data";
        }
    }
}
