# Feature Specification: Internal Trading Chart Test Data Generator for Laravel + KLineCharts

## 1. Objective

Implement an internal feature for the existing Laravel project to generate, store, modify, and stream fake trading chart data for testing purposes.

The chart library used on the frontend is:

- KLineCharts

Project information:
Laravel: 13.5.0
PHP: 8.4

This feature is intended for internal testing only and must not be treated as real trading data.

---

## 2. Main Goals

Build a complete internal testing system that can:

1. Generate fake K-line / candlestick data automatically.
2. Store generated chart data in the database.
3. Continue generating candles over time without requiring client requests.
4. Provide REST APIs to fetch chart data.
5. Provide APIs to modify future generated data direction.
6. Provide an API to rewrite an existing candle range for testing.
7. Provide real-time updates to the frontend through WebSocket.
8. Provide a demo frontend page using KLineCharts.

---

## 3. Important Requirements

### 3.1 Automatic Background Generation

The system must automatically generate chart data over time.

Example:

- For interval `1m`, generate or update candles every 1 minute.
- For interval `5m`, generate or update candles every 5 minutes.
- The process must continue without requiring any client request.

IMPORTANT:

Laravel normally runs only when receiving HTTP requests, so this feature must use a background process.

Acceptable implementation options:

1. Laravel Scheduler + Queue Worker
2. Laravel Command running continuously
3. Supervisor-managed Laravel worker
4. External Node.js service
5. External Go/Python service
6. Any reliable background service that can generate data independently

Preferred implementation:

- Use Laravel Artisan Command + Scheduler or Queue Worker first.
- If real-time streaming is easier with Node.js, it is acceptable to implement a small Node.js WebSocket service.
- The generator must be able to resume safely from the latest stored candle after restart or crash.

Claude should choose a clean and maintainable architecture.

---

## 4. Chart Data Type

The generated data must be compatible with KLineCharts.

Each candle should include:

- timestamp
- open
- high
- low
- close
- volume

KLineCharts usually expects data similar to:

```json
{
  "timestamp": 1710000000000,
  "open": 100.0,
  "high": 105.0,
  "low": 98.0,
  "close": 102.0,
  "volume": 1200
}
```

Use millisecond timestamp in API and WebSocket responses.

---

## 5. Database Design

Create a table to store generated candles.

Recommended table name:

`trading_chart_candles`

---

## 6. Table: trading_chart_candles

| Field | Type | Required | Notes |
|------|------|----------|------|
| id | bigint | yes | primary key |
| symbol | string | yes | example: BTC_USDT, ETH_USDT |
| interval | string | yes | example: 1m, 5m, 15m, 1h |
| timestamp | bigint | yes | candle timestamp in milliseconds |
| open | decimal(24,8) | yes | open price |
| high | decimal(24,8) | yes | high price |
| low | decimal(24,8) | yes | low price |
| close | decimal(24,8) | yes | close price |
| volume | decimal(24,8) | yes | candle volume |
| direction | string | yes | up, down, neutral |
| status | string | yes | open, closed |
| is_generated | boolean | yes | default true |
| is_modified | boolean | yes | default false |
| created_at | timestamp | yes | |
| updated_at | timestamp | yes | |

### Indexes

Add indexes:

- symbol
- interval
- timestamp
- status
- unique(symbol, interval, timestamp)

---

## 7. Optional Table: trading_chart_generation_rules

Create this table if needed to control future data generation.

Recommended table name:

`trading_chart_generation_rules`

### Columns

| Field | Type | Required | Notes |
|------|------|----------|------|
| id | bigint | yes | primary key |
| symbol | string | yes | example: BTC_USDT |
| interval | string | yes | example: 1m |
| forced_direction | string | no | up, down, neutral |
| price_bias_percent | decimal(10,4) | no | positive or negative bias |
| active_from | bigint | no | timestamp in milliseconds |
| active_until | bigint | no | timestamp in milliseconds |
| apply_to_existing | boolean | yes | default false |
| is_active | boolean | yes | default true |
| created_at | timestamp | yes | |
| updated_at | timestamp | yes | |

This table is used to control future generated candles.

---

## 8. Business Rules

1. Generated candles must be continuous by time.
2. There must be no duplicated candle for the same symbol, interval, and timestamp.
3. The next candle should start from the previous candle close price.
4. Candle prices must be realistic enough for testing.
5. The generator should support at least:
   - up trend
   - down trend
   - neutral/random trend
6. API must allow modifying future generated data direction.
7. Modified future data should affect candles that have not happened yet.
8. Existing historical candles may be modified only by the rewrite-range API.
9. Real-time socket updates must push new and updated candles to connected clients.
10. REST API must support fetching historical candle list.
11. Candle values must always be valid:
    - low <= open <= high
    - low <= close <= high
    - high >= low
12. When rewriting a candle range, preserve time continuity:
    - current candle open should equal previous candle close where possible
    - next candle open should be recalculated if required
13. Limit heavy operations:
    - maximum 1000 candles per API response unless explicitly configured
    - maximum 1000 candles per rewrite request unless explicitly configured
14. The generator must safely resume from the latest candle after restart.
15. Initial price must be configurable per symbol.

---

## 9. Supported Symbols

Initial symbols:

- BTC_USDT
- ETH_USDT
- SOL_USDT

Claude may make this configurable.

Suggested initial prices:

- BTC_USDT: 60000
- ETH_USDT: 3000
- SOL_USDT: 150

---

## 10. Supported Intervals

Initial intervals:

- 1m
- 5m

Optional future intervals:

- 15m
- 1h
- 4h
- 1d

Generation frequency:

- `1m`: generate/finalize one candle every 1 minute.
- `5m`: generate/finalize one candle every 5 minutes.

---

## 11. Data Generation Logic

### 11.1 Initial Seed

Create a command to seed initial candle data.

Example:

```bash
php artisan chart:seed --symbol=BTC_USDT --interval=1m --days=7 --initial-price=60000
```

This should generate historical test data for the past 7 days.

### 11.2 Continuous Generation

Create a background command or scheduled process.

Example:

```bash
php artisan chart:generate
```

Or:

```bash
php artisan chart:worker
```

The generator should:

1. Read the latest candle for each symbol and interval.
2. Generate or update the current candle.
3. Finalize the candle when the interval ends.
4. Generate the next candle.
5. Save candles to database.
6. Broadcast candle updates through WebSocket.
7. Continue automatically.

### 11.3 Current Candle vs Closed Candle

The system should support two candle states:

- `open`: the current candle still being updated in real time.
- `closed`: the completed candle after the interval ends.

Real-time updates may update the current candle multiple times before it is closed.

### 11.4 Example Generation Formula

For each new candle:

- open = previous close
- close = open + random movement
- high = max(open, close) + random upper wick
- low = min(open, close) - random lower wick
- volume = random volume

Trend direction:

- up: close should usually be higher than open
- down: close should usually be lower than open
- neutral: random

---

## 12. API Requirements

All APIs should follow the existing project response standard if available.

If no standard exists, use this format.

### Success Response

```json
{
  "success": true,
  "status_code": 200,
  "code": "SUCCESS",
  "data": {}
}
```

### Error Response

```json
{
  "success": false,
  "status_code": 400,
  "code": "CHART_INVALID_REQUEST",
  "message": "Invalid request.",
  "errors": {}
}
```

---

## 13. API 1: Get Candle List

### Endpoint

```http
GET /api/internal/chart/candles
```

### Query Params

| Param | Required | Example |
|------|----------|---------|
| symbol | yes | BTC_USDT |
| interval | yes | 1m |
| from | no | 1710000000000 |
| to | no | 1710100000000 |
| limit | no | 500 |

### Rules

- Default limit: 500
- Maximum limit: 1000
- Return candles ordered by timestamp ascending

### Response

```json
{
  "success": true,
  "status_code": 200,
  "code": "CHART_CANDLES_FETCHED",
  "data": [
    {
      "timestamp": 1710000000000,
      "open": 100.0,
      "high": 105.0,
      "low": 98.0,
      "close": 102.0,
      "volume": 1200
    }
  ]
}
```

---

## 14. API 2: Modify Future Data Direction

This API allows internal users to change the direction of future generated candles.

### Endpoint

```http
POST /api/internal/chart/future-direction
```

### Request Body

```json
{
  "symbol": "BTC_USDT",
  "interval": "1m",
  "direction": "up",
  "from_timestamp": 1710000000000,
  "to_timestamp": 1710000300000,
  "price_bias_percent": 1.5,
  "apply_to_existing": false
}
```

### Direction Values

- up
- down
- neutral

### Behavior

- Save a generation rule into `trading_chart_generation_rules`.
- The generator must apply this rule when creating future candles.
- By default, this API only affects future candles.
- If `apply_to_existing = true`, candles that already exist in the selected range may be recalculated and marked as modified.
- If `apply_to_existing = false`, existing candles must not be changed.

### Response

```json
{
  "success": true,
  "status_code": 200,
  "code": "CHART_FUTURE_DIRECTION_UPDATED",
  "data": {
    "symbol": "BTC_USDT",
    "interval": "1m",
    "direction": "up",
    "apply_to_existing": false
  }
}
```

---

## 15. API 3: Modify Existing Candle Range

This API allows internal users to directly rewrite already generated candle data in a selected time range.

### Endpoint

```http
POST /api/internal/chart/rewrite-range
```

### Request Body

```json
{
  "symbol": "BTC_USDT",
  "interval": "1m",
  "from_timestamp": 1710000000000,
  "to_timestamp": 1710000300000,
  "direction": "down",
  "strength": 2.0
}
```

### Behavior

- Find candles in the selected range.
- Rewrite open/high/low/close using selected direction and strength.
- Ensure candle values are valid:
  - low <= open <= high
  - low <= close <= high
  - high >= low
- Preserve continuity where possible:
  - current candle open should equal previous candle close
  - next candle open may be recalculated if required
- Mark updated candles:
  - is_modified = true
- Broadcast updated candles through WebSocket if needed.

### Limits

- Maximum rewrite range: 1000 candles by default.

### Response

```json
{
  "success": true,
  "status_code": 200,
  "code": "CHART_RANGE_REWRITTEN",
  "data": {
    "updated_count": 5
  }
}
```

---

## 16. WebSocket Requirements

The system must support real-time candle updates.

### Requirements

- Client connects to WebSocket server.
- Client subscribes to a symbol and interval.
- Server pushes current candle updates.
- Server pushes closed candle events.
- Server can also push updated candles if future/existing data changes.

### Suggested Channel

```text
chart.BTC_USDT.1m
```

### Event Names

```text
candle.update
candle.close
candle.rewrite
```

### Payload

```json
{
  "event": "candle.update",
  "symbol": "BTC_USDT",
  "interval": "1m",
  "data": {
    "timestamp": 1710000000000,
    "open": 100.0,
    "high": 105.0,
    "low": 98.0,
    "close": 102.0,
    "volume": 1200,
    "status": "open"
  }
}
```

---

## 17. Real-time Technology Options

Claude may choose one of the following approaches.

### Option A: Laravel Reverb

Use Laravel broadcasting with Laravel Reverb.

Good if the Laravel version supports it.

### Option B: Laravel Echo + Pusher-compatible Server

Use Laravel Echo with a WebSocket server.

### Option C: Node.js WebSocket Service

Create a separate Node.js service using:

- ws
- socket.io
- uWebSockets.js

This service can:

- read new candles from Redis/database
- broadcast to frontend
- run independently from Laravel HTTP requests

### Preferred Recommendation

For simplicity and maintainability:

- Laravel handles database, APIs, and generation commands.
- WebSocket can be Laravel Reverb or Node.js service.
- Use Redis pub/sub if Laravel generator needs to notify Node.js WebSocket server.

---

## 18. Background Service Requirements

The generator must not rely on client requests.

### Laravel Scheduler

Add schedule in Laravel:

```php
$schedule->command('chart:generate')->everyMinute();
```

### Supervisor

Use Supervisor to keep worker running:

```bash
php artisan chart:worker
```

### External Worker

Use Node.js or another service if better.

### Crash Handling

The generator must:

- resume from the latest stored candle
- skip duplicated timestamps
- avoid generating duplicate candles
- log errors safely
- continue running after non-critical failures

---

## 19. Demo Frontend Page

Create a demo page to display KLineCharts.

### Requirements

1. Load initial candles from REST API.
2. Initialize KLineCharts.
3. Connect to WebSocket.
4. Update chart in real time when receiving new candle data.
5. Allow selecting:
   - symbol
   - interval

### Suggested Route

```http
GET /internal/chart-demo
```

### Frontend Requirements

Use:

- KLineCharts
- JavaScript
- React if already used in the project
- otherwise plain JS is acceptable

### KLineCharts Update Rules

Use:

- `applyNewData` for initial candle list.
- `updateData` for real-time candle updates.

### Example Frontend Flow

1. Fetch initial candles:

```http
GET /api/internal/chart/candles?symbol=BTC_USDT&interval=1m&limit=500
```

2. Initialize chart with `applyNewData`.
3. Connect WebSocket.
4. Subscribe to `chart.BTC_USDT.1m`.
5. On `candle.update`, call `updateData`.
6. On `candle.close`, call `updateData`.
7. On `candle.rewrite`, reload affected range or refresh chart data.

---

## 20. Suggested File Structure

Claude should create or update files similar to:

```text
app/Models/TradingChartCandle.php
app/Models/TradingChartGenerationRule.php

database/migrations/create_trading_chart_candles_table.php
database/migrations/create_trading_chart_generation_rules_table.php

app/Console/Commands/SeedTradingChartCandles.php
app/Console/Commands/GenerateTradingChartCandles.php
app/Console/Commands/TradingChartWorker.php

app/Services/TradingChart/CandleGeneratorService.php
app/Services/TradingChart/ChartRuleService.php
app/Services/TradingChart/ChartBroadcastService.php

app/Http/Controllers/Internal/TradingChartController.php
app/Http/Requests/Internal/GetCandlesRequest.php
app/Http/Requests/Internal/UpdateFutureDirectionRequest.php
app/Http/Requests/Internal/RewriteCandleRangeRequest.php

routes/api.php
routes/web.php

resources/views/internal/chart-demo.blade.php

app/Events/TradingChartCandleUpdated.php
```

If the project uses a different folder structure, follow the current project convention.

---

## 21. Error Codes

Add centralized error codes if the project already has an error code system.

Suggested codes:

```text
CHART_CANDLES_FETCHED
CHART_FUTURE_DIRECTION_UPDATED
CHART_RANGE_REWRITTEN
CHART_INVALID_SYMBOL
CHART_INVALID_INTERVAL
CHART_INVALID_DIRECTION
CHART_INVALID_TIMESTAMP_RANGE
CHART_RANGE_TOO_LARGE
CHART_CANDLE_NOT_FOUND
CHART_GENERATION_FAILED
CHART_WEBSOCKET_ERROR
CHART_INTERNAL_ERROR
```

---

## 22. Security Requirements

This is an internal test feature.

Protect all internal APIs with appropriate middleware.

Recommended:

- auth middleware
- admin/internal middleware
- IP allowlist if available
- rate limit for modification APIs

Do not expose this feature publicly.

---

## 23. Validation Rules

### Symbol

Must be one of supported symbols.

Example:

- BTC_USDT
- ETH_USDT
- SOL_USDT

### Interval

Must be one of supported intervals.

Example:

- 1m
- 5m

### Direction

Must be one of:

- up
- down
- neutral

### Timestamp Range

- from_timestamp must be less than to_timestamp
- timestamp must be milliseconds
- range must not exceed the configured max candle count

---

## 24. Acceptance Criteria

The feature is complete when:

1. Database tables are created.
2. Initial candles can be seeded.
3. Background generator can generate new candles automatically.
4. Generator can resume from latest candle after restart.
5. Candles can be fetched by REST API.
6. Future direction can be changed by API.
7. Existing candle range can be rewritten by API.
8. WebSocket broadcasts current candle updates.
9. WebSocket broadcasts closed candles.
10. Demo page renders KLineCharts successfully.
11. Demo page updates in real time.
12. Internal APIs are protected.
13. Code is clean and maintainable.

---

## 25. Expected Output from Claude

Claude must return the implementation in this order:

1. Short architecture summary
2. List of files to create or modify
3. Migrations
4. Models
5. Services
6. Artisan commands
7. Controllers
8. Request validation classes
9. Routes
10. WebSocket/broadcasting implementation
11. Demo frontend page using KLineCharts
12. Setup instructions
13. Testing instructions
14. Supervisor or scheduler setup instructions

---

## 26. Final Instruction

Implement this feature in the safest and cleanest way for the existing Laravel project.

Prioritize:

- maintainability
- internal testing reliability
- real-time chart update support
- clear separation between data generation, API, WebSocket, and frontend demo

Do not overcomplicate trading logic. This is fake internal chart data for testing, not a real trading engine.
