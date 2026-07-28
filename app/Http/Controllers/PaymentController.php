<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\Gateways\MockGateway;
use App\Services\Payments\Gateways\RazorpayGateway;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        $gatewayName = $request->input('gateway', 'mock');
        $paymentId = $request->input('payment_id');

        $payment = Payment::findOrFail($paymentId);
        $paymentService = app(PaymentService::class);

        // Resolve gateway
        if ($gatewayName === 'razorpay') {
            $paymentService->setGateway(new RazorpayGateway());
        } else {
            $paymentService->setGateway(new MockGateway());
        }

        try {
            $isSuccess = $paymentService->handleCallback($request, $payment);
            
            if ($isSuccess) {
                return view('public.players.payment-success', compact('payment'));
            } else {
                return view('public.players.payment-failed', compact('payment'));
            }
        } catch (\Exception $e) {
            return view('public.players.payment-failed', ['payment' => $payment, 'error' => $e->getMessage()]);
        }
    }
}
