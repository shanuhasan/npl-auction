<x-ipl-layout>
    <x-slot name="title">Payment Successful</x-slot>

    <div class="container mx-auto px-4 py-12 flex justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center border-t-4 border-green-500">
            <div class="mb-4 text-green-500">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Registration Complete!</h2>
            <p class="text-gray-600 mb-6">Your payment was successful and your player registration is now under review.</p>
            
            <div class="bg-gray-50 p-4 rounded-lg text-left mb-6">
                <p class="text-sm text-gray-700"><strong>Player Name:</strong> {{ $payment->player->name }}</p>
                <p class="text-sm text-gray-700"><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                <p class="text-sm text-gray-700"><strong>Amount:</strong> ₹{{ number_format($payment->amount, 2) }}</p>
            </div>

            <a href="{{ route('home') }}" class="inline-block bg-primary text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:bg-primary-dark transition duration-300">
                Return to Home
            </a>
        </div>
    </div>
</x-ipl-layout>
