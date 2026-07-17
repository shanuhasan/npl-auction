<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Players List</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 32px; text-transform: uppercase; color: #D4AF37; }
        .header p { margin: 5px 0 0 0; font-size: 16px; color: #666; }
        
        table.players { width: 100%; border-collapse: collapse; }
        table.players th, table.players td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: middle; }
        table.players th { background: #f1f1f1; text-transform: uppercase; font-size: 12px; }
        
        .photo { width: 50px; height: 50px; border-radius: 25px; object-fit: cover; }
        
        .footer { position: fixed; bottom: -10px; width: 100%; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        
        /* Utility */
        .text-center { text-align: center; }
        .capitalize { text-transform: capitalize; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Registered Players</h1>
        <p>Official {{ setting('app_name', 'NPLT20') }} Players List</p>
    </div>

    <table class="players">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Role</th>
                <th>Base Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $player)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @php
                            $imagePath = $player->photo ? public_path('storage/' . $player->photo) : null;
                            $imageData = null;
                            if ($imagePath && file_exists($imagePath)) {
                                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                                $data = file_get_contents($imagePath);
                                $imageData = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp
                        @if($imageData)
                            <img src="{{ $imageData }}" class="photo" alt="Photo">
                        @else
                            <div style="width: 50px; height: 50px; border-radius: 25px; background: #eee; text-align: center; line-height: 50px; font-weight: bold; color: #999;">
                                {{ substr($player->name, 0, 1) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $player->name }}</strong><br>
                        <span style="font-size: 12px; color: #666;">{{ $player->city ? $player->city . ', ' : '' }}{{ $player->country }}</span>
                    </td>
                    <td class="capitalize">{{ $player->role }}</td>
                    <td>Rs. {{ number_format($player->base_price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y, H:i A') }} by {{ setting('app_name', 'NPLT20') }}.
    </div>

</body>
</html>
