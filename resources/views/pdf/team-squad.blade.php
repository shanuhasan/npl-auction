<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $team->name }} Squad</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid {{ $team->primary_color }}; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 32px; text-transform: uppercase; color: {{ $team->primary_color }}; }
        .header p { margin: 5px 0 0 0; font-size: 16px; color: #666; }
        
        .stats { width: 100%; margin-bottom: 30px; }
        .stats td { width: 33%; text-align: center; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; }
        .stats h3 { margin: 0; font-size: 14px; text-transform: uppercase; color: #777; }
        .stats p { margin: 5px 0 0 0; font-size: 20px; font-weight: bold; color: #111; }
        
        .role-section { margin-bottom: 30px; }
        .role-title { background: {{ $team->primary_color }}; color: #fff; padding: 10px; text-transform: uppercase; margin-bottom: 10px; }
        
        table.squad { width: 100%; border-collapse: collapse; }
        table.squad th, table.squad td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        table.squad th { background: #f1f1f1; text-transform: uppercase; font-size: 12px; }
        
        .footer { position: absolute; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $team->name }}</h1>
        <p>Official NPLT20 Squad</p>
    </div>

    <table class="stats">
        <tr>
            <td>
                <h3>Total Budget</h3>
                <p>₹{{ number_format($team->budget) }}</p>
            </td>
            <td>
                <h3>Total Spent</h3>
                <p>₹{{ number_format($totalSpent) }}</p>
            </td>
            <td>
                <h3>Purse Remaining</h3>
                <p>₹{{ number_format($remainingBudget) }}</p>
            </td>
        </tr>
    </table>

    @foreach(['batsman', 'all-rounder', 'wicketkeeper', 'bowler'] as $role)
        @if(isset($playersByRole[$role]) && $playersByRole[$role]->count() > 0)
            <div class="role-section">
                <div class="role-title">{{ str_replace('-', ' ', $role) }}s ({{ $playersByRole[$role]->count() }})</div>
                <table class="squad">
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Country</th>
                            <th>Category</th>
                            <th>Base Price</th>
                            <th>Bought For</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($playersByRole[$role] as $player)
                            <tr>
                                <td><strong>{{ $player->name }}</strong></td>
                                <td>{{ $player->country }}</td>
                                <td>{{ ucfirst($player->category) }}</td>
                                <td>₹{{ number_format($player->base_price) }}</td>
                                <td>
                                    @php
                                        $bought = $player->auctionPlayers->first();
                                    @endphp
                                    <strong>₹{{ $bought ? number_format($bought->final_price) : number_format($player->base_price) }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <div class="footer">
        Generated on {{ now()->format('d M Y, H:i A') }} by Naugawan Premier League (NPLT20).
    </div>

</body>
</html>
