<?php

namespace App\Services\Payments\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\Player;
use Illuminate\Http\Request;

use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Facades\Log;

class RazorpayGateway implements PaymentGatewayInterface
{
    protected $api;

    public function __construct()
    {
        $keyId = config('services.razorpay.key');
        $keySecret = config('services.razorpay.secret');
        
        if ($keyId && $keySecret) {
            $this->api = new Api($keyId, $keySecret);
        }
    }

    public function initializePayment(Player $player, Payment $payment)
    {
        if (!$this->api) {
            throw new Exception('Razorpay keys are not configured. Please add RAZORPAY_KEY and RAZORPAY_SECRET to your .env file.');
        }

        // Amount must be in paise (multiply by 100)
        $amountInPaise = (int) ($payment->amount * 100);

        $orderData = [
            'receipt'         => 'rcptid_' . $payment->id,
            'amount'          => $amountInPaise,
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        try {
            $razorpayOrder = $this->api->order->create($orderData);
            
            // Update the transaction ID with the Razorpay order ID
            $payment->update(['transaction_id' => $razorpayOrder['id']]);

            return redirect()->route('payment.checkout', ['payment' => $payment->id]);
        } catch (Exception $e) {
            Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
            throw new Exception('Could not initialize payment. Please try again later.');
        }
    }

    public function verifyPayment(Request $request): bool
    {
        if (!$this->api) {
            return false;
        }

        try {
            $attributes = array(
                'razorpay_order_id' => $request->input('razorpay_order_id'),
                'razorpay_payment_id' => $request->input('razorpay_payment_id'),
                'razorpay_signature' => $request->input('razorpay_signature')
            );

            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch(Exception $e) {
            Log::error('Razorpay Signature Verification Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getGatewayName(): string
    {
        return 'razorpay';
    }
}
