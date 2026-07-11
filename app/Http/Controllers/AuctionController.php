<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuctionService;

class AuctionController extends Controller
{
    protected $auctionService;

    public function __construct(AuctionService $auctionService)
    {
        $this->auctionService = $auctionService;
    }

    public function startAuction($auctionId)
    {
        $result = $this->auctionService->startAuction($auctionId);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function nextPlayer($auctionId)
    {
        $result = $this->auctionService->nextPlayer($auctionId);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function placeBid(Request $request)
    {
        $request->validate([
            'auction_id' => 'required|exists:auctions,id',
            'team_id' => 'required|exists:teams,id',
            'bid_amount' => 'required|numeric|min:0'
        ]);

        $result = $this->auctionService->placeBid(
            $request->auction_id,
            $request->team_id,
            $request->bid_amount
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function markSold(Request $request, $auctionPlayerId)
    {
        $result = $this->auctionService->markSold($auctionPlayerId);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function markUnsold($auctionPlayerId)
    {
        $result = $this->auctionService->markUnsold($auctionPlayerId);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function endAuction($auctionId)
    {
        $result = $this->auctionService->endAuction($auctionId);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
