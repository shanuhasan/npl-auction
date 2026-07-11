<?php

namespace App\Livewire\Public\Players;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Player;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $category = '';
    public $status = '';

    public function updating($property)
    {
        if (in_array($property, ['search', 'role', 'category', 'status'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Player::with(['currentTeam', 'auctionPlayers' => function($q) {
            $q->where('status', 'sold')->latest();
        }]);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        if ($this->role) {
            $query->where('role', $this->role);
        }
        if ($this->category) {
            $query->where('category', $this->category);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        $players = $query->orderBy('name')->paginate(16);

        return view('livewire.public.players.index', [
            'players' => $players
        ])->layout(auth()->check() ? 'layouts.app' : 'layouts.public');
    }
}
