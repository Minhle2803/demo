<?php

namespace App\Http\Controllers\Trade;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trade\PlaceTradeRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Trade;
use App\Models\TradingSession;
use App\Services\Trading\TradeService;
use App\Services\Trading\TradingSessionService;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradingSessionController extends Controller
{
    public function __construct(
        protected TradingSessionService $sessionService,
        protected TradeService $tradeService,
    ) {}

    /**
     * GET /api/trade/session/current
     */
    public function current(): JsonResponse
    {
        $session = $this->sessionService->getCurrentSession();

        if (! $session) {
            return ApiResponse::error(ErrorCodes::TRADE_SESSION_NOT_FOUND, 404);
        }

        return ApiResponse::success([
            'session' => $this->formatSession($session),
            'server_time' => now()->toIso8601String(),
        ], ErrorCodes::TRADE_SESSION_FETCHED);
    }

    /**
     * POST /api/trade/buy
     */
    public function buy(PlaceTradeRequest $request): JsonResponse
    {
        return $this->placeTrade($request, 'buy');
    }

    /**
     * POST /api/trade/sell
     */
    public function sell(PlaceTradeRequest $request): JsonResponse
    {
        return $this->placeTrade($request, 'sell');
    }

    /**
     * GET /api/trade/session/{id}/result
     */
    public function result(int $id): JsonResponse
    {
        $session = TradingSession::find($id);

        if (! $session) {
            return ApiResponse::error(ErrorCodes::TRADE_SESSION_NOT_FOUND, 404);
        }

        $user = auth('client')->user();
        if (! $user) {
            $user = request()->user();
        }
        $trade = $session->trades()->where('user_id', $user->id)->first();

        return ApiResponse::success([
            'session' => $this->formatSession($session),
            'trade' => $trade ? [
                'type' => $trade->type,
                'amount' => $trade->amount,
                'status' => $trade->status,
                'payout' => $trade->payout,
            ] : null,
        ], ErrorCodes::TRADE_RESULT_FETCHED);
    }

    protected function placeTrade(PlaceTradeRequest $request, string $type): JsonResponse
    {
        $session = $this->sessionService->getCurrentSession();

        if (! $session) {
            return ApiResponse::error(ErrorCodes::TRADE_SESSION_NOT_FOUND, 404);
        }

        try {
            $trade = $this->tradeService->placeTrade(
                $request->user(),
                $session,
                $type,
                (float) $request->validated('amount')
            );

            return ApiResponse::success([
                'trade' => [
                    'id' => $trade->id,
                    'type' => $trade->type,
                    'amount' => $trade->amount,
                    'status' => $trade->status,
                    'session_id' => $trade->session_id,
                ],
            ], ErrorCodes::TRADE_PLACE_SUCCESS, 201);
        } catch (\Exception $e) {
            $code = $e->getMessage();
            $httpStatus = match ($code) {
                ErrorCodes::USER_NOT_FULLY_VERIFIED => 403,
                ErrorCodes::TRADE_SESSION_LOCKED,
                ErrorCodes::TRADE_SESSION_NOT_OPEN => 422,
                ErrorCodes::TRADE_ALREADY_PLACED => 409,
                ErrorCodes::TRADE_INSUFFICIENT_BALANCE => 422,
                default => 500,
            };

            return ApiResponse::error(
                code: $code,
                message: __('errors.'.$code),
                statusCode: $httpStatus
            );
        }
    }

    protected function formatSession(TradingSession $session): array
    {
        return [
            'id' => $session->id,
            'status' => $session->status,
            'start_time' => $session->start_time->toIso8601String(),
            'lock_time' => $session->lock_time->toIso8601String(),
            'end_time' => $session->end_time->toIso8601String(),
            'open_price' => $session->open_price,
            'close_price' => $session->close_price,
        ];
    }

    public function latest(Request $request)
    {
        $lastId = $request->get('last_id', 0);

        $trades = Trade::query()
            ->join('trading_sessions', 'trades.session_id', '=', 'trading_sessions.id')
            ->select([
                'trades.id',
                'trades.session_id',
                'trades.type',
                'trades.status',
                'trades.amount',
                'trades.payout',
                'trades.created_at',

                'trading_sessions.symbol as session_symbol',
                'trading_sessions.open_price as session_open_price',
                'trading_sessions.close_price as session_close_price',
            ])
            ->where('trades.id', '>=', $lastId)
            ->orderByDesc('trades.session_id')
            ->orderByDesc('trades.id')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $trades,
        ]);
    }
}
