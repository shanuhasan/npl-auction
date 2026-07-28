<x-ipl-layout>
    <x-slot name="title">Processing Payment...</x-slot>

    <div class="container mx-auto px-4 py-12 flex justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center border-t-4 border-[#FFC800]">
            <div class="mb-4 text-[#FFC800]">
                <svg class="w-16 h-16 mx-auto animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Redirecting to Secure Payment</h2>
            <p class="text-gray-600 mb-6">Please do not refresh or close this window.</p>
            
            <form action="{{ route('payment.callback') }}" method="POST" id="razorpay-form" class="hidden">
                @csrf
                <input type="hidden" name="gateway" value="razorpay">
                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                <script
                    src="https://checkout.razorpay.com/v1/checkout.js"
                    data-key="{{ $key }}"
                    data-amount="{{ $payment->amount * 100 }}"
                    data-currency="INR"
                    data-order_id="{{ $order_id }}"
                    data-buttontext="Pay with Razorpay"
                    data-name="{{ setting('app_name', 'NPL') }}"
                    data-description="Player Registration Fee"
                    data-image="{{ setting('logo') ? asset('storage/' . setting('logo')) : '' }}"
                    data-prefill.name="{{ $player->name }}"
                    data-prefill.contact="{{ $player->contact_no }}"
                    data-theme.color="#FFC800">
                </script>
            </form>
        </div>
    </div>

    <!-- Auto-submit Razorpay Checkout -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var razorpayButton = document.querySelector('.razorpay-payment-button');
                if(razorpayButton) {
                    razorpayButton.click();
                }
            }, 1000);
        });
    </script>
</x-ipl-layout>
