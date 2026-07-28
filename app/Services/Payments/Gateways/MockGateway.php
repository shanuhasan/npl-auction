<?php

namespace App\Services\Payments\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MockGateway implements PaymentGatewayInterface
{
    public function initializePayment(Player $player, Payment $payment)
    {
        // For mock, we simply redirect to the callback route with a dummy transaction ID
        // and success status.
        $transactionId = 'mock_tx_' . Str::random(10);
        $payment->update(['transaction_id' => $transactionId]);

        return redirect()->route('payment.callback', [
            'gateway' => $this->getGatewayName(),
            'transaction_id' => $transactionId,
            'status' => 'success',
            'payment_id' => $payment->id,
        ]);
    }

    public function verifyPayment(Request $request): bool
    {
        // In a real gateway, this would verify the signature or call the gateway API.
        // For the mock, we just check if status is success.
        return $request->input('status') === 'success';
    }

    public function getGatewayName(): string
    {
        return 'mock';
    }
}
