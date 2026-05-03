# Feature Specification: Spot Crypto Trading System with Admin Manual Matching

## Overview

Implement a normal crypto spot trading system similar to Binance spot trading.

Users can place buy/sell orders for crypto pairs such as:

- BTC/USDT
- ETH/USDT

This is NOT the previous 60-second session-based prediction trading feature.

This is a new and separate feature.

Do not reuse session trading logic.

This is a real order-based spot trading system.

---

## Core Concept

Users place orders:

- Buy order: user wants to buy crypto using USDT
- Sell order: user wants to sell crypto and receive USDT

Orders can be matched in two ways:

1. Automatically matched with another user order
2. Manually matched by admin from the admin panel if no matching user order is available

---

## First Version Scope

Implement:

- Limit Buy Order
- Limit Sell Order
- Auto matching
- Partial fills
- Order cancellation
- Wallet locking
- Wallet settlement
- Trade history
- Wallet ledger
- Admin manual matching

Market order can be added later.

---

## Important Note

This feature must be separated from:

- session-based trading
- 60-second trading sessions
- prediction trading logic

This feature is normal spot trading.

---

## Main Trading Rules

### Buy Order

When a user places a buy order:

- User selects trading pair
- User enters price
- User enters quantity
- Total cost = price × quantity
- System checks quote asset balance, usually USDT
- System locks/reserves the required quote asset amount
- Create buy order with status `open`
- Try to auto-match against open sell orders

Example:

```text
BTC/USDT
price = 65000
quantity = 0.01

required_usdt = 65000 × 0.01 = 650 USDT
```

---

### Sell Order

When a user places a sell order:

- User selects trading pair
- User enters price
- User enters quantity
- System checks base asset balance, for example BTC
- System locks/reserves the base asset quantity
- Create sell order with status `open`
- Try to auto-match against open buy orders

Example:

```text
BTC/USDT
quantity = 0.01

required_btc = 0.01 BTC
```

---

## Supported Trading Pairs

Initial supported pairs:

- BTC_USDT
- ETH_USDT

Make supported pairs configurable.

Recommended config file:

```text
config/spot_trading.php
```

Example:

```php
return [
    'symbols' => [
        'BTC_USDT' => [
            'base_asset' => 'BTC',
            'quote_asset' => 'USDT',
            'price_precision' => 2,
            'quantity_precision' => 8,
            'min_quantity' => '0.00000001',
            'min_notional' => '5',
        ],
        'ETH_USDT' => [
            'base_asset' => 'ETH',
            'quote_asset' => 'USDT',
            'price_precision' => 2,
            'quantity_precision' => 8,
            'min_quantity' => '0.00000001',
            'min_notional' => '5',
        ],
    ],
];
```

---

## Database Design

### Table: crypto_wallets

Stores user balances per asset.

Fields:

| Column | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| user_id | bigint | client user id |
| asset | string | BTC, ETH, USDT |
| available_balance | decimal(36,18) | spendable balance |
| locked_balance | decimal(36,18) | reserved in open orders |
| created_at | timestamp | |
| updated_at | timestamp | |

Unique:

```text
user_id + asset
```

---

### Table: crypto_orders

Stores buy/sell limit orders.

Fields:

| Column | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| user_id | bigint | client user id |
| symbol | string | BTC_USDT |
| base_asset | string | BTC |
| quote_asset | string | USDT |
| side | enum | buy, sell |
| type | enum | limit |
| price | decimal(36,18) | limit price |
| quantity | decimal(36,18) | original quantity |
| filled_quantity | decimal(36,18) | matched quantity |
| remaining_quantity | decimal(36,18) | quantity not matched yet |
| total_amount | decimal(36,18) | price × quantity |
| status | enum | open, partially_filled, filled, cancelled |
| created_at | timestamp | |
| updated_at | timestamp | |

Recommended indexes:

- symbol
- side
- status
- price
- created_at
- user_id

---

### Table: crypto_trades

Stores matched trade history.

Fields:

| Column | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| symbol | string | BTC_USDT |
| buy_order_id | bigint nullable | |
| sell_order_id | bigint nullable | |
| buyer_user_id | bigint nullable | |
| seller_user_id | bigint nullable | |
| admin_user_id | bigint nullable | admin manual match actor |
| source | enum | auto_match, admin_manual |
| price | decimal(36,18) | execution price |
| quantity | decimal(36,18) | executed quantity |
| total | decimal(36,18) | price × quantity |
| created_at | timestamp | |

---

### Table: crypto_wallet_transactions

Stores wallet ledger.

Fields:

| Column | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| user_id | bigint | |
| asset | string | BTC, ETH, USDT |
| type | string | lock, unlock, debit, credit, trade_buy, trade_sell, cancel_unlock |
| amount | decimal(36,18) | |
| balance_before | decimal(36,18) | available balance before |
| balance_after | decimal(36,18) | available balance after |
| locked_before | decimal(36,18) | locked balance before |
| locked_after | decimal(36,18) | locked balance after |
| reference_type | string nullable | order/trade/admin_manual |
| reference_id | bigint nullable | |
| created_at | timestamp | |

---

## Order Matching Logic

### Buy matches Sell when:

```text
buy.price >= sell.price
```

### Sell matches Buy when:

```text
sell.price <= buy.price
```

### Matching price rule

Use maker order price by default.

If implementing a simpler first version, use the sell order price as execution price.

The selected rule must be documented in code comments.

---

## Partial Fill Requirement

Partial fill must be supported.

Example:

```text
Buy order quantity: 1 BTC
Sell order quantity: 0.4 BTC

Matched quantity = 0.4 BTC

Buy order:
- filled_quantity = 0.4
- remaining_quantity = 0.6
- status = partially_filled

Sell order:
- filled_quantity = 0.4
- remaining_quantity = 0
- status = filled
```

---

## Wallet Locking Logic

### Buy Order Lock

When placing a buy order:

```text
required_quote = price × quantity
```

Move quote asset:

```text
available_balance -= required_quote
locked_balance += required_quote
```

Example:

```text
Buy BTC/USDT
User locks USDT
```

---

### Sell Order Lock

When placing a sell order:

```text
required_base = quantity
```

Move base asset:

```text
available_balance -= quantity
locked_balance += quantity
```

Example:

```text
Sell BTC/USDT
User locks BTC
```

---

## Settlement Logic

### Auto Match Settlement

For matched quantity:

```text
total = execution_price × matched_quantity
```

Buyer:

```text
locked quote asset decreases by total
available base asset increases by matched_quantity
```

Seller:

```text
locked base asset decreases by matched_quantity
available quote asset increases by total
```

---

## Buy Order Refund Difference

If buy order locked more quote asset than actual execution price requires, refund the difference.

Example:

```text
Buy order price = 65000
Execution price = 64000
Quantity = 0.01

Locked = 650
Used = 640
Refund = 10 USDT
```

Refund:

```text
buyer.available_quote += refund
buyer.locked_quote -= refund
```

---

## Admin Manual Matching

If no matching user order exists, admin can manually match an open order.

Admin can:

- View open buy/sell orders
- Select an order
- Match it manually
- Confirm execution price
- Confirm execution quantity

When admin manually matches:

- The selected order is filled or partially filled
- User balance is updated
- A trade history record is created
- The counterparty is marked as `admin`

This allows the system to execute trades even when there is no opposite user order.

---

## Admin Manual Match Rules

- Admin matching must be recorded clearly
- Trade source must be `admin_manual`
- Admin user ID must be saved
- Execution price must be saved
- Execution quantity must be saved
- Admin cannot match more than remaining order quantity
- Admin cannot execute invalid price or quantity
- Admin cannot match cancelled or already filled orders
- Admin manual matches must be auditable

---

## Admin Manual Match Settlement

### If admin matches a user buy order

User receives base asset.

User locked quote asset is reduced based on execution total.

If execution price is lower than order price, refund quote asset difference.

Trade history is created with:

```text
source = admin_manual
buyer_user_id = user_id
seller_user_id = null
admin_user_id = admin id
```

---

### If admin matches a user sell order

User locked base asset is reduced.

User receives quote asset.

Trade history is created with:

```text
source = admin_manual
buyer_user_id = null
seller_user_id = user_id
admin_user_id = admin id
```

---

## User APIs

### Create Buy Order

```http
POST /api/spot/orders/buy
```

Request:

```json
{
  "symbol": "BTC_USDT",
  "price": "65000",
  "quantity": "0.01"
}
```

---

### Create Sell Order

```http
POST /api/spot/orders/sell
```

Request:

```json
{
  "symbol": "BTC_USDT",
  "price": "65000",
  "quantity": "0.01"
}
```

---

### Cancel Order

```http
POST /api/spot/orders/{id}/cancel
```

---

### My Orders

```http
GET /api/spot/orders
```

Query params:

- symbol optional
- status optional

---

### My Trade History

```http
GET /api/spot/trades
```

---

### My Wallets

```http
GET /api/spot/wallets
```

---

## Admin APIs

### Open Orders

```http
GET /api/admin/spot/orders/open
```

Query params:

- symbol optional
- side optional
- status optional

---

### Manual Match Order

```http
POST /api/admin/spot/orders/{id}/manual-match
```

Request:

```json
{
  "price": "65000",
  "quantity": "0.01"
}
```

---

## Validation Rules

### User Validation

- User must be authenticated
- User must be fully verified
- KYC must exist:
  - kyc_front_url not empty
  - kyc_back_url not empty
- Symbol must be supported
- Price must be greater than 0
- Quantity must be greater than 0
- Order notional must be greater than minimum notional
- User must have enough available balance
- Never trust frontend balances

---

### Admin Validation

- Admin must be authenticated
- Admin must have permission to manually match orders
- Quantity must not exceed remaining order quantity
- Price must be greater than 0
- Order must be open or partially filled
- Order must not be cancelled
- Order must not be filled

---

## Anti-Cheat / Safety Rules

- Never trust frontend balance
- Use DB transactions for all order and wallet operations
- Use row-level locking when updating wallets
- Prevent negative available balance
- Prevent negative locked balance
- Prevent double matching
- Prevent cancelling filled orders
- Prevent matching cancelled orders
- Prevent matching more than remaining quantity
- All balance changes must be recorded in wallet ledger
- Admin manual matches must be auditable
- Use decimal-safe calculations
- Avoid float calculations for money

---

## Recommended Laravel Architecture

### Models

```text
app/Models/CryptoWallet.php
app/Models/CryptoOrder.php
app/Models/CryptoTrade.php
app/Models/CryptoWalletTransaction.php
```

---

### Services

```text
app/Services/SpotTrading/WalletService.php
app/Services/SpotTrading/OrderService.php
app/Services/SpotTrading/MatchingService.php
app/Services/SpotTrading/AdminManualMatchService.php
```

---

### Controllers

```text
app/Http/Controllers/Api/Spot/SpotOrderController.php
app/Http/Controllers/Api/Spot/SpotWalletController.php
app/Http/Controllers/Api/Spot/SpotTradeController.php
app/Http/Controllers/Admin/Spot/AdminSpotOrderController.php
```

---

### Requests

```text
app/Http/Requests/Spot/CreateBuyOrderRequest.php
app/Http/Requests/Spot/CreateSellOrderRequest.php
app/Http/Requests/Spot/CancelOrderRequest.php
app/Http/Requests/Admin/Spot/AdminManualMatchRequest.php
```

---

## Frontend JavaScript Requirement

Frontend JavaScript must be split into ES modules using Laravel Vite.

Do not put all JavaScript in one Blade file.

Suggested structure:

```text
resources/js/spot-trading/
├── api.js
├── state.js
├── orders.js
├── wallets.js
├── ui.js
├── validation.js
└── main.js
```

Responsibilities:

- api.js: all API calls
- state.js: shared frontend state
- orders.js: create/cancel orders
- wallets.js: load wallet balances
- ui.js: DOM rendering
- validation.js: frontend input validation
- main.js: bootstrap page and bind events

Load only on the spot trading page:

```blade
@vite(['resources/js/spot-trading/main.js'])
```

---

## Error Codes

Use centralized error codes.

Suggested codes:

```text
SPOT_INVALID_SYMBOL
SPOT_INVALID_PRICE
SPOT_INVALID_QUANTITY
SPOT_MIN_NOTIONAL_NOT_MET
SPOT_INSUFFICIENT_BALANCE
SPOT_ORDER_NOT_FOUND
SPOT_ORDER_ALREADY_FILLED
SPOT_ORDER_ALREADY_CANCELLED
SPOT_ORDER_CANCEL_FAILED
SPOT_MATCH_FAILED
SPOT_ADMIN_MATCH_FAILED
SPOT_UNAUTHORIZED
SPOT_USER_NOT_FULLY_VERIFIED
SPOT_WALLET_NOT_FOUND
SPOT_NEGATIVE_BALANCE_BLOCKED
```

---

## Response Format

Use the existing project API response standard.

Example success:

```json
{
  "success": true,
  "code": "SPOT_ORDER_CREATED",
  "data": {}
}
```

Example error:

```json
{
  "success": false,
  "code": "SPOT_INSUFFICIENT_BALANCE",
  "message": "Insufficient balance."
}
```

---

## UI:

resources> views > pages > spot_crypto_trading.php

div#tradeSection :

This is where the UI is created for the trading UI.

div#marketList

This is where the datatable history is created.

## Expected Output From Claude

Claude must generate step-by-step:

1. Architecture overview
2. File structure
3. Database migrations
4. Models and relationships
5. Wallet service
6. Order service
7. Matching service
8. Admin manual matching service
9. User API controllers
10. Admin API controllers
11. Request validation classes
12. Routes
13. JS modules using Laravel Vite
14. Testing examples

Do NOT generate all code at once.

First generate:

- architecture overview
- file structure
- database design

Wait for confirmation before generating code.

---

## Final Instruction For Claude

Read this spec carefully.

This is a NEW feature and must be separate from the previous session-based trading feature.

Do not reuse session trading logic.

First propose architecture and file structure only.

Do not generate implementation code yet.
