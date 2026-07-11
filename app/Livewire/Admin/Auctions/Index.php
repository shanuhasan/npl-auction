<?php

namespace App\Livewire\Admin\Auctions;

use Livewire\Component;
use App\Models\Auction;
use App\Models\AuctionState;

class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.auctions.index', [
            'auctions' => Auction::latest()->get(),
        ])->layout('layouts.app');
    }

    public function startAuction($auctionId)
    {
        $auction = Auction::findOrFail($auctionId);
        
        if ($auction->status === 'upcoming') {
            $auction->update(['status' => 'live']);
            
            // Note: Auction state is created at the time of auction creation.
            // But we should ensure it exists just in case.
            if (!$auction->state) {
                AuctionState::create([
                    'auction_id' => $auction->id,
                    'timer_seconds' => 15,
                    'bid_increment_rule' => [
                        ['upto' => 100, 'increment' => 10],
                        ['upto' => 500, 'increment' => 25],
                    ],
                ]);
            }
            
            session()->flash('message', 'Auction started successfully!');
        } else {
            session()->flash('error', 'Auction is already ' . $auction->status);
        }
    }
}
