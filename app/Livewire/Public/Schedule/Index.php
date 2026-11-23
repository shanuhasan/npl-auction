<?php

namespace App\Livewire\Public\Schedule;

use App\Models\Auction;
use App\Models\Fixture;
use App\Services\FixtureScheduler;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Match Schedule')]
class Index extends Component
{
    #[Url(as: 'auction')]
    public $auctionGuid = '';

    #[Url(as: 'team')]
    public $teamId = '';

    #[Url]
    public $tab = 'fixtures';

    public function mount()
    {
        if (!$this->auctionGuid) {
            // Default: latest auction that has a schedule, otherwise the latest auction
            $auction = Auction::whereHas('fixtures')->latest('auction_date')->first()
                ?? Auction::latest('auction_date')->first();
            $this->auctionGuid = $auction?->guid ?? '';
        }
    }

    public function updatedAuctionGuid()
    {
        $this->teamId = '';
    }

    public function render(FixtureScheduler $scheduler)
    {
        $auctions = Auction::latest('auction_date')->get();
        $auction = $auctions->firstWhere('guid', $this->auctionGuid);

        $fixtures = collect();
        $teams = collect();
        $pointsTable = collect();

        if ($auction) {
            $allFixtures = Fixture::with(['teamA', 'teamB', 'winner'])
                ->where('auction_id', $auction->id)
                ->orderByRaw('match_date IS NULL')
                ->orderBy('match_date')
                ->orderBy('match_no')
                ->get();

            $teams = $allFixtures->flatMap(fn ($f) => [$f->teamA, $f->teamB])->filter()->unique('id')->sortBy('name')->values();
            $fixtures = $this->teamId
                ? $allFixtures->filter(fn ($f) => $f->team_a_id == $this->teamId || $f->team_b_id == $this->teamId)
                : $allFixtures;
            $pointsTable = $scheduler->pointsTable($auction, $teams);
        }

        return view('livewire.public.schedule.index', [
            'auctions' => $auctions,
            'auction' => $auction,
            'teams' => $teams,
            'fixturesByDay' => $fixtures->groupBy(fn ($f) => $f->match_date?->toDateString() ?? 'tbd'),
            'pointsTable' => $pointsTable,
        ])->layout('layouts.ipl');
    }
}
