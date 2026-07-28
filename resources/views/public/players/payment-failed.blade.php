<x-ipl-layout>
    <x-slot name="title">Payment Failed</x-slot>

    <div class="container mx-auto px-4 py-12 flex justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center border-t-4 border-red-500">
            <div class="mb-4 text-red-500">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Payment Failed</h2>
            <p class="text-gray-600 mb-6">Unfortunately, your payment could not be processed. Please try again or contact support.</p>
            
            @if(isset($error))
                <div class="bg-red-50 text-red-700 p-4 rounded-lg text-sm text-left mb-6">
                    <strong>Error:</strong> {{ $error }}
                </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg text-left mb-6">
                <p class="text-sm text-gray-700"><strong>Player Name:</strong> {{ $payment->player->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-700"><strong>Transaction ID:</strong> {{ $payment->transaction_id ?? 'N/A' }}</p>
            </div>

            <a href="{{ route('public.players.register') }}" class="inline-block bg-primary text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:bg-primary-dark transition duration-300">
                Try Again
            </a>
        </div>
    </div>
</x-ipl-layout>
