<?php

namespace App\Http\Controllers\Api\Admin\Spot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spot\AdminManualMatchRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CryptoOrder;
use App\Services\SpotTrading\AdminManualMatchService;
use App\Services\SpotTrading\OrderBookService;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSpotOrderController extends Controller
{
    public function __construct(
        private readonly AdminManualMatchService $adminMatchService,
        private readonly OrderBookService $orderBookService,
    ) {}

    public function openOrders(Request $request): JsonResponse
    {
        $query = CryptoOrder::with('user')
            ->whereIn('status', ['open', 'partially_filled'])
            ->latest();

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->input('symbol'));
        }

        if ($request->filled('side')) {
            $query->where('side', $request->input('side'));
        }

        $orders = $query->paginate($request->input('per_page', 20));

        return ApiResponse::success(data: $orders);
    }

    public function manualMatch(int $id, AdminManualMatchRequest $request): JsonResponse
    {
        $order = CryptoOrder::find($id);

        if (! $order) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_ORDER_NOT_FOUND,
                message: __('errors.'.ErrorCodes::SPOT_ORDER_NOT_FOUND),
                statusCode: 404,
            );
        }

        $result = $this->adminMatchService->execute(
            order: $order,
            price: (string) $request->input('price'),
            quantity: (string) $request->input('quantity'),
            admin: $request->user(),
        );

        if ($result['code'] === ErrorCodes::SPOT_ADMIN_MATCH_SUCCESS) {
            $this->orderBookService->broadcast($order->symbol);

            return ApiResponse::success(
                data: $result['order']->toArray(),
                code: ErrorCodes::SPOT_ADMIN_MATCH_SUCCESS,
            );
        }

        return ApiResponse::error(
            code: $result['code'],
            message: __('errors.'.$result['code']),
            statusCode: 400,
        );
    }
}
