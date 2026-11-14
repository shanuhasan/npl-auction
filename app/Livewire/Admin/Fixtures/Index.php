<?php

namespace App\Livewire\Admin\Fixtures;

use App\Models\Auction;
use App\Models\Fixture;
use App\Services\FixtureScheduler;
use Livewire\Component;

class Index extends Component
{
    public Auction $auction;

    // Generator
    public array $selectedTeams = [];
    public string $format = 'single';
    public string $startDate = '';
    public string $matchTimes = '10:00, 14:00';
    public int $dayGap = 1;
    public string $venue = '';
    public bool $replace = true;

    // Filter
    public $filterTeam = '';

    // Add / edit match modal
    public bool $showModal = false;
    public ?int $editId = null;
    public $match_no;
    public $stage = 'League';
    public $team_a_id = '';
    public $team_b_id = '';
    public $match_date = '';
    public $match_venue = '';
    public $status = 'scheduled';
    public $team_a_score = '';
    public $team_b_score = '';
    public $winner_team_id = '';
    public $result = '';

    public function mount(Auction $auction, FixtureScheduler $scheduler)
    {
        $this->auction = $auction;
        $this->selectedTeams = $scheduler->teamsFor($auction)->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->startDate = now()->addDay()->toDateString();
    }

    public function generate(FixtureScheduler $scheduler)
    {
        $this->validate([
            'selectedTeams' => 'required|array|min:2',
            'selectedTeams.*' => 'exists:teams,id',
            'format' => 'required|in:single,double',
            'startDate' => 'required|date',
            'matchTimes' => ['required', 'regex:/^\s*\d{1,2}:\d{2}\s*(,\s*\d{1,2}:\d{2}\s*)*$/'],
            'dayGap' => 'required|integer|min:1|max:30',
            'venue' => 'nullable|string|max:255',
        ], [
            'selectedTeams.min' => 'Select at least 2 teams.',
            'matchTimes.regex' => 'Enter times like 10:00, 14:00',
        ]);

        $count = $scheduler->generate($this->auction, $this->selectedTeams, [
            'format' => $this->format,
            'start_date' => $this->startDate,
            'match_times' => array_map('trim', explode(',', $this->matchTimes)),
            'day_gap' => $this->dayGap,
            'venue' => $this->venue ?: null,
            'replace' => $this->replace,
        ]);

        session()->flash('message', "Schedule generated: {$count} matches created.");
    }

    public function clearAll()
    {
        Fixture::where('auction_id', $this->auction->id)->delete();
        session()->flash('message', 'All matches deleted.');
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->match_no = (int) Fixture::where('auction_id', $this->auction->id)->max('match_no') + 1;
        $this->match_venue = $this->venue;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $f = Fixture::where('auction_id', $this->auction->id)->findOrFail($id);
        $this->editId = $f->id;
        $this->match_no = $f->match_no;
        $this->stage = $f->stage;
        $this->team_a_id = (string) $f->team_a_id;
        $this->team_b_id = (string) $f->team_b_id;
        $this->match_date = $f->match_date?->format('Y-m-d\TH:i') ?? '';
        $this->match_venue = $f->venue ?? '';
        $this->status = $f->status;
        $this->team_a_score = $f->team_a_score ?? '';
        $this->team_b_score = $f->team_b_score ?? '';
        $this->winner_team_id = (string) ($f->winner_team_id ?? '');
        $this->result = $f->result ?? '';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'match_no' => 'required|integer|min:1',
            'stage' => 'required|string|max:50',
            'team_a_id' => 'required|exists:teams,id',
            'team_b_id' => 'required|exists:teams,id|different:team_a_id',
            'match_date' => 'nullable|date',
            'match_venue' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,live,completed,abandoned',
            'team_a_score' => 'nullable|string|max:50',
            'team_b_score' => 'nullable|string|max:50',
            'winner_team_id' => 'nullable|in:' . $this->team_a_id . ',' . $this->team_b_id,
            'result' => 'nullable|string|max:255',
        ], [
            'team_b_id.different' => 'A team cannot play against itself.',
            'winner_team_id.in' => 'Winner must be one of the two teams.',
        ]);

        $data = [
            'auction_id' => $this->auction->id,
            'match_no' => $this->match_no,
            'stage' => $this->stage,
            'team_a_id' => $this->team_a_id,
            'team_b_id' => $this->team_b_id,
            'match_date' => $this->match_date ?: null,
            'venue' => $this->match_venue ?: null,
            'status' => $this->status,
            'team_a_score' => $this->team_a_score ?: null,
            'team_b_score' => $this->team_b_score ?: null,
            'winner_team_id' => $this->status === 'completed' && $this->winner_team_id ? $this->winner_team_id : null,
            'result' => $this->result ?: null,
        ];

        if ($this->editId) {
            Fixture::where('auction_id', $this->auction->id)->findOrFail($this->editId)->update($data);
            session()->flash('message', 'Match updated.');
        } else {
            Fixture::create($data);
            session()->flash('message', 'Match added.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Fixture::where('auction_id', $this->auction->id)->findOrFail($id)->delete();
        session()->flash('message', 'Match deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset(['editId', 'match_no', 'stage', 'team_a_id', 'team_b_id', 'match_date', 'match_venue',
            'status', 'team_a_score', 'team_b_score', 'winner_team_id', 'result']);
        $this->resetValidation();
    }

    public function render(FixtureScheduler $scheduler)
    {
        $teams = $scheduler->teamsFor($this->auction);
        $allTeams = \App\Models\Team::orderBy('name')->get();

        $fixtures = Fixture::with(['teamA', 'teamB', 'winner'])
            ->where('auction_id', $this->auction->id)
            ->when($this->filterTeam, fn ($q) => $q->forTeam($this->filterTeam))
            ->orderByRaw('match_date IS NULL')
            ->orderBy('match_date')
            ->orderBy('match_no')
            ->get();

        return view('livewire.admin.fixtures.index', [
            'teams' => $teams,
            'allTeams' => $allTeams,
            'fixturesByDay' => $fixtures->groupBy(fn ($f) => $f->match_date?->toDateString() ?? 'tbd'),
            'totalFixtures' => $fixtures->count(),
        ])->layout('layouts.app');
    }
}
