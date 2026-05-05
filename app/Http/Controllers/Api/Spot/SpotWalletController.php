<?php

namespace App\Http\Controllers\Api\Spot;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\CryptoWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SpotWalletController extends Controller
{
    public function myWallets(): JsonResponse
    {
        $user = Auth::guard('client')->user();
        if (! $user) {
            $user = request()->user();
        }

        $wallets = CryptoWallet::where('user_id', $user->id)->get();

        return ApiResponse::success(data: $wallets);
    }
}
