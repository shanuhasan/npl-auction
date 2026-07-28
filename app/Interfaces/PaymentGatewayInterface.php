<?php

namespace App\Interfaces;

use App\Models\Payment;
use App\Models\Player;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment request and return a response
     * (e.g., a redirect URL, a view with checkout form, or JSON data).
     *
     * @param Player $player
     * @param Payment $payment
     * @return mixed
     */
    public function initializePayment(Player $player, Payment $payment);

    /**
     * Verify the payment callback from the gateway.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyPayment(Request $request): bool;

    /**
     * Get a unique identifier for the gateway.
     *
     * @return string
     */
    public function getGatewayName(): string;
}
