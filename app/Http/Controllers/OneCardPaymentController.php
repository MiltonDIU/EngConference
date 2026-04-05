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

        // Store transaction ID and return URL in session
        session(['onecard_reff_id' => $transaction_id]);
        session(['payment_return_url' => url()->previous()]);

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
        Log::info('OneCard IPN Received:', $request->all());
        $reff_id = $request->input('reff_id') ?? $request->input('id') ?? $request->input('tran_id');
        
        if (!$reff_id) {
            Log::warning('OneCard Push: No transaction ID received');
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
        Log::info('OneCard Redirect Received:', $request->all());
        
        // Try to get transaction ID from request first, then fall back to session
        $reff_id = $request->input('reff_id') ?? $request->input('id') ?? $request->input('tran_id') ?? session('onecard_reff_id');
        
        if (!$reff_id) {
            Log::warning('OneCard Redirect: No transaction ID found in request or session.');
            return redirect()->route('fail')->with('message', 'Transaction identification lost.');
        }

        $returnUrl = session('payment_return_url') ?? route('show-profile');

        // Check DB first (IPN might have already processed this)
        $order = DB::table('orders')->where('transaction_id', $reff_id)->first();
        
        if ($order && ($order->status == 'Processing' || $order->status == 'Complete')) {
            return redirect($returnUrl)->with('success', 'Payment successful! Your registration is confirmed.');
        } elseif ($order && $order->status == 'Failed') {
            return redirect($returnUrl)->with('error', 'Payment failed. Please try again.');
        } elseif ($order && $order->status == 'Canceled') {
            return redirect($returnUrl)->with('error', 'Payment was canceled.');
        } else {
            // Check verification manually if push hasn't arrived yet or status is still pending
            $validationResponse = Http::asForm()->post($this->verificationUrl, [
                'reff_id' => $reff_id,
                'token' => $this->token
            ]);

            if ($validationResponse->successful()) {
                $result = $validationResponse->json();
                $status = $result['data']['status'] ?? '';

                if (isset($result['message']) && $result['message'] == 'success' && $status == 'VALIDATED') {
                    $this->processSuccessfulPayment($reff_id, $result);
                    return redirect($returnUrl)->with('success', 'Payment successful! Your registration is confirmed.');
                } elseif ($status == 'CANCELLED' || $status == 'INVALID') {
                    $this->updateStatus($reff_id, 'Canceled', 3, $result);
                    return redirect($returnUrl)->with('error', 'Your payment was canceled.');
                } else {
                    $this->updateStatus($reff_id, 'Failed', 2, $result);
                }
            } else {
                $this->updateStatus($reff_id, 'Failed', 2, ['error' => 'Validation request failed', 'body' => $validationResponse->body()]);
            }
            
            return redirect($returnUrl)->with('error', 'Payment verification failed or was denied.');
        }
    }

    /**
     * Handle user-initiated cancellation.
     */
    public function cancel(Request $request)
    {
        Log::info('OneCard Cancel Route Hit:', $request->all());
        $reff_id = $request->input('reff_id') ?? $request->input('id') ?? $request->input('tran_id') ?? session('onecard_reff_id');
        
        if ($reff_id) {
            $this->updateStatus($reff_id, 'Canceled', 3, ['info' => 'User hit cancel route']);
        }
        
        $returnUrl = session('payment_return_url') ?? route('show-profile');
        return redirect($returnUrl)->with('error', 'Your payment was canceled.');
    }

    /**
     * Handle payment failure.
     */
    public function fail(Request $request)
    {
        Log::info('OneCard Fail Route Hit:', $request->all());
        $reff_id = $request->input('reff_id') ?? $request->input('id') ?? $request->input('tran_id') ?? session('onecard_reff_id');
        
        if ($reff_id) {
            $this->updateStatus($reff_id, 'Failed', 2, ['info' => 'User hit fail route']);
        }
        
        $returnUrl = session('payment_return_url') ?? route('show-profile');
        return redirect($returnUrl)->with('error', 'Payment failed. Please try again.');
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
    }

    /**
     * Admin tool to manually verify all payment attempts for a user.
     */
    public function verifyPayment(Request $request)
    {
        $profile_id = $request->input('profile_id');
        $profile = Profile::findOrFail($profile_id);
        $user = $profile->user;

        if (!$user) {
            return back()->with('error', 'User not found for this profile.');
        }

        // Find all onecard payments for this user
        $payments = DB::table('payments')
            ->where('user_id', $user->id)
            ->where('getaway', 'onecard')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($payments->isEmpty()) {
            return back()->with('error', 'No OneCard payment attempts found for this user.');
        }

        $checkedCount = 0;
        foreach ($payments as $payment) {
            $checkedCount++;
            $reff_id = $payment->reff_id;

            // Verify with OneCard API
            $validationResponse = Http::asForm()->post($this->verificationUrl, [
                'reff_id' => $reff_id,
                'token' => $this->token
            ]);

            if ($validationResponse->successful()) {
                $result = $validationResponse->json();
                $status = $result['data']['status'] ?? '';

                if (isset($result['message']) && $result['message'] == 'success' && $status == 'VALIDATED') {
                    $this->processSuccessfulPayment($reff_id, $result);
                    return back()->with('success', "Payment verified successfully using Ref: $reff_id!");
                }
            }
        }

        return back()->with('error', "Checked $checkedCount payment attempts, but no successful transaction was found on OneCard.");
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
