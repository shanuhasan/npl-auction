<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Payment;
use App\Models\Player;
use Exception;
use Illuminate\Http\Request;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    /**
     * Set the payment gateway implementation.
     *
     * @param PaymentGatewayInterface $gateway
     */
    public function setGateway(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Initialize a payment for a player registration.
     *
     * @param Player $player
     * @param float $amount
     * @return mixed
     * @throws Exception
     */
    public function initializePlayerRegistration(Player $player, float $amount)
    {
        if (!isset($this->gateway)) {
            throw new Exception("Payment gateway not configured.");
        }

        // Create a pending payment record
        $payment = Payment::create([
            'player_id' => $player->id,
            'amount' => $amount,
            'gateway' => $this->gateway->getGatewayName(),
            'status' => 'pending',
        ]);

        return $this->gateway->initializePayment($player, $payment);
    }

    /**
     * Handle the callback from the payment gateway.
     *
     * @param Request $request
     * @param Payment $payment
     * @return bool
     * @throws Exception
     */
    public function handleCallback(Request $request, Payment $payment): bool
    {
        if (!isset($this->gateway)) {
            throw new Exception("Payment gateway not configured.");
        }

        if ($this->gateway->verifyPayment($request)) {
            // Payment successful
            $payment->update(['status' => 'completed']);
            $payment->player->update([
                'payment_status' => 'completed',
                'is_approved' => true
            ]);
            return true;
        } else {
            // Payment failed
            $payment->update(['status' => 'failed']);
            $payment->player->update(['payment_status' => 'failed']);
            return false;
        }
    }
}
