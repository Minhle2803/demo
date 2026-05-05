<?php

namespace App\Http\Controllers\Api\Spot;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\CryptoTrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpotTradeController extends Controller
{
    public function myTrades(Request $request): JsonResponse
    {
        $user = Auth::guard('client')->user();

        if (!$user) {
            $user = $request->user();
        }

        $query = CryptoTrade::where(function ($q) use ($user) {
            $q->where('buyer_user_id', $user->id)
                ->orWhere('seller_user_id', $user->id);
        })->latest('created_at');

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->input('symbol'));
        }

        $trades = $query->paginate($request->input('per_page', 20));

        return ApiResponse::success(data: $trades);
    }
}
