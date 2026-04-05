<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Profile;
use App\Models\Paper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationConfirmed;
use App\Services\IdGeneratorService;

class OneCardPaymentController extends Controller
{
    protected $token = 'eV8XmAz3UYReT68Xnns1IFBBLfXOkrwppg';
    protected $apiUrl = 'https://api.1card.com.bd/isbsp/pay';
    protected $verificationUrl = 'https://api.1card.com.bd/isbsp/validationserverapi';

    /**
     * Initiate the payment process.
     */
    public function index(Request $request, $user, $transaction_id)
    {
        $post_data = [];
        
        if ($request->input('is_paper_checkout')) {
            $post_data['amount'] = $request->input('calculated_amount');
            $post_data['currency'] = $request->input('calculated_currency');
            $paper_ids_json = json_encode($request->input('checkout_paper_ids'));
        } else {
            $paper_ids_json = null;
            $profile = $user->profile;
            $post_data['amount'] = $profile->pay_amount ?? 0;
            $post_data['currency'] = $profile->currency ?? "BDT";
        }

        // DB Update for order tracking (matching SSLCommerz pattern)
        DB::table('orders')->updateOrInsert(
            ['transaction_id' => $transaction_id],
            [
                'name' => $user->name,
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $user->profile->whatsapp_number ?? "01811458857",
                'amount' => $post_data['amount'],
                'status' => 'Pending',
                'address' => 'Dhaka',
                'currency' => $post_data['currency'],
                'paper_ids' => $paper_ids_json,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $params = [
            'user_id' => $user->id,
            'amount' => $post_data['amount'],
            'currency' => $post_data['currency'],
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_address' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1205',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $user->profile->whatsapp_number ?? "01811458857",
            'success' => route('onecard.success'),
            'redirect' => route('onecard.redirect'),
            'cancel' => route('cancel'),
            'fail' => route('fail'),
            'reff_id' => $transaction_id,
            'response_type' => 'json',
        ];

        try {
            $response = Http::asForm()->post($this->apiUrl, $params);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['message']) && $data['message'] == 'success' && isset($data['url'])) {
                    return redirect()->away($data['url']);
                }
                Log::error('OneCard Initiation Error: ' . json_encode($data));
                Log::error('OneCard Full Response: ' . $response->body());
                return back()->with('error', 'Payment initiation failed. Please try again.');
            }
            
            Log::error('OneCard HTTP Error: ' . $response->body());
            return back()->with('error', 'Could not connect to payment gateway.');
        } catch (\Exception $e) {
            Log::error('OneCard Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while initiating payment.');
        }
    }

    /**
     * Handle the server-to-server callback (Push API).
     */
    public function success(Request $request)
    {
        $reff_id = $request->input('reff_id');
        
        if (!$reff_id) {
            Log::warning('OneCard Push: No reff_id received');
            return response()->json(['message' => 'failed']);
        }

        // Verify the payment
        $validationResponse = Http::asForm()->post($this->verificationUrl, [
            'reff_id' => $reff_id,
            'token' => $this->token
        ]);

        if ($validationResponse->successful()) {
            $result = $validationResponse->json();
            
            if (isset($result['message']) && $result['message'] == 'success' && $result['data']['status'] == 'VALIDATED') {
                $this->processSuccessfulPayment($reff_id, $result);
                return response()->json(['message' => 'success']);
            } else {
                $this->updateStatus($reff_id, 'Failed', 2, $result);
            }
        } else {
            Log::error('OneCard Push Validation Failed for ' . $reff_id . ': ' . $validationResponse->body());
        }
        
        return response()->json(['message' => 'failed']);
    }

    /**
     * User redirection handler.
     */
    public function redirect(Request $request)
    {
        $reff_id = $request->input('reff_id');
        
        // Wait a small bit for server push if needed, or just check DB
        $order = DB::table('orders')->where('transaction_id', $reff_id)->first();
        
        if ($order && ($order->status == 'Processing' || $order->status == 'Complete')) {
            return redirect()->route('success')->with('message', 'Transaction is successfully Completed');
        } elseif ($order && $order->status == 'Failed') {
            return redirect()->route('fail')->with('message', 'Transaction has failed');
        } else {
            // Check verification manually if push hasn't arrived yet
            $validationResponse = Http::asForm()->post($this->verificationUrl, [
                'reff_id' => $reff_id,
                'token' => $this->token
            ]);

            if ($validationResponse->successful()) {
                $result = $validationResponse->json();
                $status = $result['data']['status'] ?? '';

                if (isset($result['message']) && $result['message'] == 'success' && $status == 'VALIDATED') {
                    $this->processSuccessfulPayment($reff_id, $result);
                    return redirect()->route('success')->with('message', 'Transaction is successfully Completed');
                } elseif ($status == 'CANCELLED') {
                    $this->updateStatus($reff_id, 'Canceled', 3, $result);
                    return redirect()->route('cancel')->with('message', 'Your payment has been canceled');
                } else {
                    $this->updateStatus($reff_id, 'Failed', 2, $result);
                }
            } else {
                $this->updateStatus($reff_id, 'Failed', 2, ['error' => 'Validation request failed', 'body' => $validationResponse->body()]);
            }
            
            return redirect()->route('fail')->with('message', 'Payment verification failed or was canceled.');
        }
    }

    /**
     * Update order and payment status for non-success cases.
     */
    protected function updateStatus($tran_id, $orderStatus, $paymentStatus, $data = null)
    {
        $message = $data ? json_encode($data) : null;
        
        DB::table('orders')->where('transaction_id', $tran_id)->update(['status' => $orderStatus, 'updated_at' => now()]);
        DB::table('payments')->where('reff_id', $tran_id)->update([
            'status' => $paymentStatus, 
            'message' => $message,
            'updated_at' => now()
        ]);
        
        $orderPayment = Payment::where('reff_id', $tran_id)->first();
        if ($orderPayment && $orderPayment->user && $orderPayment->user->profile) {
            $orderPayment->user->profile->update(['payment_status' => (string)$paymentStatus]);
        }
    }

    /**
     * Shared logic for success processing.
     */
    protected function processSuccessfulPayment($tran_id, $fullResponse)
    {
        $order = DB::table('orders')->where('transaction_id', $tran_id)->first();
        $data = $fullResponse['data'];
        $message = json_encode($fullResponse);
        
        if (!$order || $order->status == 'Processing' || $order->status == 'Complete') {
            return; // Already processed
        }

        $amount = $data['amount'];
        $currency = $data['currency'];

        // Update orders table
        DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->update(['status' => 'Processing', 'updated_at' => now()]);

        // Update payments table
        DB::table('payments')
            ->where('reff_id', $tran_id)
            ->update([
                'status' => 1,
                'message' => $message,
                'updated_at' => now()
            ]);

        $orderPayment = Payment::where('reff_id', $tran_id)->first();
        if (!$orderPayment) return;
        
        $profile = $orderPayment->user->profile;

        if ($order->paper_ids) {
            $pIds = json_decode($order->paper_ids, true);
            if (is_array($pIds) && count($pIds) > 0) {
                Paper::whereIn('id', $pIds)->update([
                    'payment_status' => '1',
                    'pay_amount' => $amount / count($pIds),
                    'currency' => $currency
                ]);
            }
        } else {
            $newId = IdGeneratorService::generateRegistrationId();
            DB::table('profiles')
                ->where('id', $profile->id)
                ->update([
                    'payment_status' => '1',
                    'registration_id' => $newId
                ]);
            $profile->refresh();
        }

        // Send Email
        try {
            $mailData = [
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'name' => $orderPayment->user->name,
                'registration_id' => $profile->registration_id,
                'category' => 'Presenter',
                'mode' => $profile->mode_of_participation ?? 'Onsite',
                'amount' => $amount,
                'currency' => $currency,
                'transaction_id' => $tran_id,
            ];
            Mail::to($orderPayment->user->email)->queue(new RegistrationConfirmed($mailData));
        } catch (\Exception $e) {
            Log::error('OneCard Mail Error: ' . $e->getMessage());
        }
    }
}
