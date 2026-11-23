<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Fixture;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FixtureScheduler
{
    /**
     * Teams taking part in an auction's season:
     * the auction's participating teams, otherwise teams that bought players in it,
     * otherwise all approved teams.
     */
    public function teamsFor(Auction $auction): Collection
    {
        $teams = $auction->teams()->orderBy('name')->get();

        if ($teams->isEmpty()) {
            $teamIds = $auction->auctionPlayers()->whereNotNull('sold_to_team_id')->distinct()->pluck('sold_to_team_id');
            $teams = Team::whereIn('id', $teamIds)->orderBy('name')->get();
        }

        if ($teams->isEmpty()) {
            $teams = Team::where('is_approved', true)->orderBy('name')->get();
        }

        return $teams;
    }

    /**
     * Round-robin pairings using the circle method.
     * Returns rounds; every team plays at most once per round.
     *
     * @return array<int, array<int, array{0:int,1:int}>>
     */
    public function roundRobin(array $teamIds, bool $double = false): array
    {
        $teams = array_values($teamIds);
        if (count($teams) % 2 === 1) {
            $teams[] = null; // bye
        }

        $n = count($teams);
        $rounds = [];

        for ($r = 0; $r < $n - 1; $r++) {
            $pairs = [];
            for ($i = 0; $i < $n / 2; $i++) {
                $home = $teams[$i];
                $away = $teams[$n - 1 - $i];
                if ($home === null || $away === null) {
                    continue;
                }
                // Alternate home/away so no team is always listed first
                $pairs[] = ($r % 2 === 0) ? [$home, $away] : [$away, $home];
            }
            $rounds[] = $pairs;

            // Rotate everyone except the first team
            $fixed = array_shift($teams);
            array_unshift($teams, array_pop($teams));
            array_unshift($teams, $fixed);
        }

        if ($double) {
            foreach ($rounds as $pairs) {
                $rounds[] = array_map(fn ($p) => [$p[1], $p[0]], $pairs);
            }
        }

        return $rounds;
    }

    /**
     * Generate and save the league schedule for an auction.
     *
     * @param array $options [
     *   'format'      => 'single'|'double',
     *   'start_date'  => 'Y-m-d',
     *   'match_times' => ['09:00', '13:00'],   // one slot per match on a match day
     *   'day_gap'     => 1,                    // days between match days
     *   'venue'       => string|null,
     *   'replace'     => bool,                 // delete existing fixtures first
     * ]
     * @return int number of fixtures created
     */
    public function generate(Auction $auction, array $teamIds, array $options): int
    {
        $teamIds = array_values(array_unique(array_map('intval', $teamIds)));
        if (count($teamIds) < 2) {
            throw new \InvalidArgumentException('At least 2 teams are required to create a schedule.');
        }

        $times = array_values(array_filter($options['match_times'] ?? []));
        if (empty($times)) {
            $times = ['10:00'];
        }
        sort($times);

        $dayGap = max(1, (int) ($options['day_gap'] ?? 1));
        $rounds = $this->roundRobin($teamIds, ($options['format'] ?? 'single') === 'double');

        // Flatten rounds into a queue; round order keeps rest days balanced.
        $queue = [];
        foreach ($rounds as $pairs) {
            foreach ($pairs as $pair) {
                $queue[] = $pair;
            }
        }

        return DB::transaction(function () use ($auction, $queue, $times, $dayGap, $options) {
            if (!empty($options['replace'])) {
                Fixture::where('auction_id', $auction->id)->delete();
            }

            $matchNo = (int) Fixture::where('auction_id', $auction->id)->max('match_no');
            $day = Carbon::parse($options['start_date'])->startOfDay();
            $created = 0;

            // Fill each match day with up to count($times) matches,
            // never letting a team play twice on the same day.
            while (!empty($queue)) {
                $playingToday = [];
                $slot = 0;

                foreach ($queue as $index => [$a, $b]) {
                    if ($slot >= count($times)) {
                        break;
                    }
                    if (isset($playingToday[$a]) || isset($playingToday[$b])) {
                        continue;
                    }

                    [$hour, $minute] = array_pad(explode(':', $times[$slot]), 2, 0);

                    Fixture::create([
                        'auction_id' => $auction->id,
                        'match_no' => ++$matchNo,
                        'stage' => 'League',
                        'team_a_id' => $a,
                        'team_b_id' => $b,
                        'match_date' => $day->copy()->setTime((int) $hour, (int) $minute),
                        'venue' => $options['venue'] ?? null,
                        'status' => 'scheduled',
                    ]);

                    $playingToday[$a] = $playingToday[$b] = true;
                    unset($queue[$index]);
                    $slot++;
                    $created++;
                }

                $queue = array_values($queue);
                $day->addDays($dayGap);
            }

            return $created;
        });
    }

    public const SEMI_FINAL_1 = 'Semi Final 1';
    public const SEMI_FINAL_2 = 'Semi Final 2';
    public const FINAL = 'Final';

    /**
     * Create Semi Final 1 (1st v 4th), Semi Final 2 (2nd v 3rd) and the Final
     * (winner SF 1 v winner SF 2) with teams "TBC". Replaces existing matches of these stages.
     * Teams are filled in later by advancePlayoffs().
     *
     * @param array $options ['start_date' => 'Y-m-d', 'match_times' => [...], 'day_gap' => 1, 'venue' => null]
     * @return int number of fixtures created
     */
    public function generatePlayoffs(Auction $auction, array $options): int
    {
        $times = array_values(array_filter($options['match_times'] ?? []));
        if (empty($times)) {
            $times = ['10:00'];
        }
        sort($times);
        $dayGap = max(1, (int) ($options['day_gap'] ?? 1));

        $at = function (Carbon $day, string $time) {
            [$hour, $minute] = array_pad(explode(':', $time), 2, 0);
            return $day->copy()->setTime((int) $hour, (int) $minute);
        };

        $day = Carbon::parse($options['start_date'])->startOfDay();
        $sf1Date = $at($day, $times[0]);
        // Both semis on the same day if there are 2 time slots, otherwise on consecutive match days
        $sf2Date = isset($times[1]) ? $at($day, $times[1]) : $at($day->copy()->addDays($dayGap), $times[0]);
        $finalDate = $at($sf2Date->copy()->startOfDay()->addDays($dayGap), $times[0]);

        $matches = [
            [self::SEMI_FINAL_1, '1st in League', '4th in League', $sf1Date],
            [self::SEMI_FINAL_2, '2nd in League', '3rd in League', $sf2Date],
            [self::FINAL, 'Winner SF 1', 'Winner SF 2', $finalDate],
        ];

        DB::transaction(function () use ($auction, $matches, $options) {
            Fixture::where('auction_id', $auction->id)
                ->whereIn('stage', [self::SEMI_FINAL_1, self::SEMI_FINAL_2, self::FINAL])
                ->delete();

            $matchNo = (int) Fixture::where('auction_id', $auction->id)->max('match_no');

            foreach ($matches as [$stage, $labelA, $labelB, $date]) {
                Fixture::create([
                    'auction_id' => $auction->id,
                    'match_no' => ++$matchNo,
                    'stage' => $stage,
                    'team_a_label' => $labelA,
                    'team_b_label' => $labelB,
                    'match_date' => $date,
                    'venue' => $options['venue'] ?? null,
                    'status' => 'scheduled',
                ]);
            }
        });

        $this->advancePlayoffs($auction);

        return count($matches);
    }

    /**
     * Fill playoff teams that are now known:
     * - once every league match is finished, empty semi-final slots get the top 4 of the points table
     * - a semi-final winner moves into the Final (SF 1 → Team A, SF 2 → Team B)
     * Matches that are live or finished are never changed.
     */
    public function advancePlayoffs(Auction $auction): void
    {
        $league = Fixture::where('auction_id', $auction->id)->where('stage', 'League')->get();
        $leagueDone = $league->isNotEmpty() && $league->every(fn ($f) => in_array($f->status, ['completed', 'abandoned']));

        if ($leagueDone) {
            $this->fillSemiFinals($auction, overwrite: false);
        }

        $final = Fixture::where('auction_id', $auction->id)->where('stage', self::FINAL)->first();
        if (!$final || $final->status !== 'scheduled') {
            return;
        }

        foreach ([self::SEMI_FINAL_1 => 'team_a_id', self::SEMI_FINAL_2 => 'team_b_id'] as $stage => $column) {
            $semi = Fixture::where('auction_id', $auction->id)->where('stage', $stage)->first();
            if ($semi && $semi->status === 'completed' && $semi->winner_team_id) {
                $final->{$column} = $semi->winner_team_id;
            }
        }

        if ($final->team_a_id && $final->team_a_id == $final->team_b_id) {
            return; // inconsistent data entered by hand; leave it for the admin
        }
        $final->save();
    }

    /**
     * Put the top 4 of the points table into the semi finals (1st v 4th, 2nd v 3rd).
     * With $overwrite = false only empty slots are filled.
     *
     * @return bool false when fewer than 4 teams are in the points table
     */
    public function fillSemiFinals(Auction $auction, bool $overwrite = true): bool
    {
        $teamIds = Fixture::where('auction_id', $auction->id)->where('stage', 'League')
            ->get(['team_a_id', 'team_b_id'])
            ->flatMap(fn ($f) => [$f->team_a_id, $f->team_b_id])
            ->filter()->unique();

        $top = $this->pointsTable($auction, Team::whereIn('id', $teamIds)->get())
            ->take(4)->pluck('team.id')->values();

        if ($top->count() < 4) {
            return false;
        }

        foreach ([self::SEMI_FINAL_1 => [$top[0], $top[3]], self::SEMI_FINAL_2 => [$top[1], $top[2]]] as $stage => [$a, $b]) {
            $semi = Fixture::where('auction_id', $auction->id)->where('stage', $stage)->first();
            if (!$semi || $semi->status !== 'scheduled') {
                continue;
            }
            if ($overwrite || !$semi->team_a_id) {
                $semi->team_a_id = $a;
            }
            if ($overwrite || !$semi->team_b_id) {
                $semi->team_b_id = $b;
            }
            if ($semi->team_a_id != $semi->team_b_id) {
                $semi->save();
            }
        }

        return true;
    }

    /**
     * League points table from completed league matches.
     * Win = 2 points, abandoned/no result/tie = 1 point each.
     */
    public function pointsTable(Auction $auction, Collection $teams): Collection
    {
        $rows = $teams->mapWithKeys(fn ($t) => [$t->id => [
            'team' => $t, 'played' => 0, 'won' => 0, 'lost' => 0, 'nr' => 0, 'points' => 0,
        ]])->all();

        $fixtures = Fixture::where('auction_id', $auction->id)
            ->where('stage', 'League')
            ->whereIn('status', ['completed', 'abandoned'])
            ->get();

        foreach ($fixtures as $f) {
            foreach ([$f->team_a_id, $f->team_b_id] as $teamId) {
                if (!isset($rows[$teamId])) {
                    continue;
                }
                $rows[$teamId]['played']++;
                if ($f->status === 'abandoned' || !$f->winner_team_id) {
                    $rows[$teamId]['nr']++;
                    $rows[$teamId]['points'] += 1;
                } elseif ($f->winner_team_id == $teamId) {
                    $rows[$teamId]['won']++;
                    $rows[$teamId]['points'] += 2;
                } else {
                    $rows[$teamId]['lost']++;
                }
            }
        }

        return collect($rows)->sort(function ($x, $y) {
            return [$y['points'], $y['won'], $x['team']->name] <=> [$x['points'], $x['won'], $y['team']->name];
        })->values();
    }
}
