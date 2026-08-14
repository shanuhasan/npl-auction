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
    public $selectedPlayer = null;
    public $showModal = false;

    public function updating($property)
    {
        if (in_array($property, ['search', 'role', 'category', 'status'])) {
            $this->resetPage();
        }
    }

    public function showPlayer($id)
    {
        $this->selectedPlayer = Player::with(['currentTeam', 'auctionPlayers' => function($q) {
            $q->where('status', 'sold')->latest();
        }])->find($id);
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPlayer = null;
    }

    protected function getBaseQuery()
    {
        $query = Player::with(['currentTeam', 'auctionPlayers' => function($q) {
            $q->where('status', 'sold')->latest();
        }])->where('is_approved', true);

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

        return $query->orderBy('name');
    }

    public function render()
    {
        $players = $this->getBaseQuery()->paginate(100);

        return view('livewire.public.players.index', [
            'players' => $players
        ])->layout('layouts.ipl');
    }

    public function nextPlayer()
    {
        if (!$this->selectedPlayer) return;
        
        $ids = $this->getBaseQuery()->pluck('id')->toArray();
        $index = array_search($this->selectedPlayer->id, $ids);
        
        if ($index !== false && isset($ids[$index + 1])) {
            $this->showPlayer($ids[$index + 1]);
        } else if (!empty($ids)) {
            $this->showPlayer($ids[0]);
        }
    }

    public function prevPlayer()
    {
        if (!$this->selectedPlayer) return;
        
        $ids = $this->getBaseQuery()->pluck('id')->toArray();
        $index = array_search($this->selectedPlayer->id, $ids);
        
        if ($index !== false && $index > 0) {
            $this->showPlayer($ids[$index - 1]);
        } else if (!empty($ids)) {
            $this->showPlayer(end($ids));
        }
    }
}
