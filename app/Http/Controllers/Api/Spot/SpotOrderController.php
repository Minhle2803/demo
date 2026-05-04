<?php

namespace App\Http\Controllers\Api\Spot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spot\CreateBuyOrderRequest;
use App\Http\Requests\Spot\CreateSellOrderRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CryptoOrder;
use App\Services\SpotTrading\OrderBookService;
use App\Services\SpotTrading\OrderService;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpotOrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderBookService $orderBookService,
    ) {}

    public function buy(CreateBuyOrderRequest $request): JsonResponse
    {
        $user = Auth::guard('client')->user();

        if (! $user->isKycVerified()) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_USER_NOT_FULLY_VERIFIED,
                message: __('errors.'.ErrorCodes::SPOT_USER_NOT_FULLY_VERIFIED),
                statusCode: 403,
            );
        }

        $symbolConfig = config('spot_trading.symbols.'.$request->input('symbol'));
        if (! $symbolConfig) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_INVALID_SYMBOL,
                message: __('errors.'.ErrorCodes::SPOT_INVALID_SYMBOL),
                statusCode: 400,
            );
        }

        // Validate min notional
        $notional = bcmul($request->input('price'), $request->input('quantity'), 18);
        if (bccomp($notional, $symbolConfig['min_notional'], 18) < 0) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_MIN_NOTIONAL_NOT_MET,
                message: __('errors.'.ErrorCodes::SPOT_MIN_NOTIONAL_NOT_MET),
                statusCode: 400,
            );
        }

        $result = $this->orderService->createBuy(
            user: $user,
            symbolConfig: $symbolConfig,
            price: (string) $request->input('price'),
            quantity: (string) $request->input('quantity'),
        );

        if ($result['code'] === ErrorCodes::SPOT_ORDER_CREATED) {
            $this->orderBookService->broadcast($request->input('symbol'));

            return ApiResponse::success(
                data: $this->orderToArray($result['order']),
                code: ErrorCodes::SPOT_ORDER_CREATED,
            );
        }

        return ApiResponse::error(
            code: $result['code'],
            message: __('errors.'.$result['code']),
            statusCode: 400,
        );
    }

    public function sell(CreateSellOrderRequest $request): JsonResponse
    {
        $user = Auth::guard('client')->user();

        if (! $user->isKycVerified()) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_USER_NOT_FULLY_VERIFIED,
                message: __('errors.'.ErrorCodes::SPOT_USER_NOT_FULLY_VERIFIED),
                statusCode: 403,
            );
        }

        $symbolConfig = config('spot_trading.symbols.'.$request->input('symbol'));
        if (! $symbolConfig) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_INVALID_SYMBOL,
                message: __('errors.'.ErrorCodes::SPOT_INVALID_SYMBOL),
                statusCode: 400,
            );
        }

        $notional = bcmul($request->input('price'), $request->input('quantity'), 18);
        if (bccomp($notional, $symbolConfig['min_notional'], 18) < 0) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_MIN_NOTIONAL_NOT_MET,
                message: __('errors.'.ErrorCodes::SPOT_MIN_NOTIONAL_NOT_MET),
                statusCode: 400,
            );
        }

        $result = $this->orderService->createSell(
            user: $user,
            symbolConfig: $symbolConfig,
            price: (string) $request->input('price'),
            quantity: (string) $request->input('quantity'),
        );

        if ($result['code'] === ErrorCodes::SPOT_ORDER_CREATED) {
            $this->orderBookService->broadcast($request->input('symbol'));

            return ApiResponse::success(
                data: $this->orderToArray($result['order']),
                code: ErrorCodes::SPOT_ORDER_CREATED,
            );
        }

        return ApiResponse::error(
            code: $result['code'],
            message: __('errors.'.$result['code']),
            statusCode: 400,
        );
    }

    public function cancel(int $id): JsonResponse
    {
        $user = Auth::guard('client')->user();
        $order = CryptoOrder::find($id);

        if (! $order) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_ORDER_NOT_FOUND,
                message: __('errors.'.ErrorCodes::SPOT_ORDER_NOT_FOUND),
                statusCode: 404,
            );
        }

        $result = $this->orderService->cancel($user, $order);

        if ($result['code'] === ErrorCodes::SPOT_ORDER_CANCELLED) {
            $this->orderBookService->broadcast($order->symbol);

            return ApiResponse::success(
                data: $this->orderToArray($result['order']),
                code: ErrorCodes::SPOT_ORDER_CANCELLED,
            );
        }

        return ApiResponse::error(
            code: $result['code'],
            message: __('errors.'.$result['code']),
            statusCode: 400,
        );
    }

    public function myOrders(Request $request): JsonResponse
    {
        $user = Auth::guard('client')->user();

        $query = CryptoOrder::where('user_id', $user->id)->latest();

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->input('symbol'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate($request->input('per_page', 20));

        return ApiResponse::success(data: $orders);
    }

    public function orderBook(Request $request): JsonResponse
    {
        $symbol = $request->input('symbol', 'BTC_USDT');

        if (! config('spot_trading.symbols.'.$symbol)) {
            return ApiResponse::error(
                code: ErrorCodes::SPOT_INVALID_SYMBOL,
                message: __('errors.'.ErrorCodes::SPOT_INVALID_SYMBOL),
                statusCode: 400,
            );
        }

        return ApiResponse::success(data: $this->orderBookService->snapshot($symbol));
    }

    private function orderToArray(CryptoOrder $order): array
    {
        return [
            'id' => $order->id,
            'symbol' => $order->symbol,
            'side' => $order->side,
            'type' => $order->type,
            'price' => (string) $order->price,
            'quantity' => (string) $order->quantity,
            'filled_quantity' => (string) $order->filled_quantity,
            'remaining_quantity' => (string) $order->remaining_quantity,
            'total_amount' => (string) $order->total_amount,
            'status' => $order->status,
            'created_at' => $order->created_at?->toIso8601String(),
        ];
    }
}
