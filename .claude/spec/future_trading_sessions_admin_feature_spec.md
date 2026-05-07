# Feature Specification: Future Trading Session Management for Admin Dashboard

## Overview

The project currently manages realtime trading sessions in:

```text
trading_sessions
```

These sessions are used by client users for realtime trading.

Current behavior:
- Client trading page uses the current realtime session.
- Chart data is generated and displayed in realtime.
- Client users only see current/past realtime chart data.

---

## New Requirement

Implement a separate session management system for admin that is synchronized with the current realtime sessions but runs ahead by approximately **10 sessions**.

Since each session is 60 seconds:

```text
10 sessions = about 10 minutes ahead
```

This means:
- Admin can view future sessions.
- Admin dashboard can display chart data ahead of realtime by 10 sessions.
- Client users must NOT see future chart/session data.
- Client trading page must continue using the existing realtime session behavior.

---

## Important Concept

There are now two timeline views:

### 1. Client Timeline

Used by client trading page.

- Uses current realtime session.
- Uses normal realtime chart data.
- Does not show future sessions.
- Does not show future chart data.

### 2. Admin Future Timeline

Used by admin dashboard/session management.

- Runs approximately 10 sessions ahead.
- Shows future sessions.
- Shows future chart data.
- Allows admin to view both current and future sessions.

---

## Core Rule

Future sessions must be synchronized with realtime sessions.

The difference is only timing:

```text
Admin future session = realtime session + 10 session offset
```

Example:

If client current session is:

```text
Session #100
```

Then admin future dashboard should be able to see up to:

```text
Session #110
```

---

## Session Statuses

Each session should have a clear status.

Required statuses:

```text
future
open
closed
```

### future

The session is generated ahead of time but has not started in realtime yet.

### open

The session is currently active in realtime.

### closed

The session has already ended.

---

## Existing Table

Current table:

```text
trading_sessions
```

Claude must inspect the existing schema before coding.

If possible, reuse this table and add fields.

If reuse is unsafe, propose a separate table.

---

# Recommended Design Option

## Option A: Reuse trading_sessions table

Add fields if missing:

```text
session_index
timeline_type
real_start_time
real_end_time
future_offset
status
```

Possible values:

```text
timeline_type = realtime / future
```

However, if the current `trading_sessions` table is already deeply tied to trading logic, use Option B.

---

## Option B: Create separate future sessions table

Recommended if safer.

Create:

```text
admin_future_trading_sessions
```

Fields:

- id
- realtime_session_id nullable
- session_index
- symbol
- interval
- start_time
- lock_time
- end_time
- status: future, open, closed
- open_price
- close_price
- candle_timestamp
- future_offset_sessions default 10
- created_at
- updated_at

This table is for admin dashboard only.

---

# Future Chart Data Requirement

Current chart data only runs in realtime.

New requirement:

Admin dashboard must be able to display chart data that is **10 sessions ahead**.

This future chart data:

- is used only on admin dashboard
- must not be exposed to client trading page
- must be synchronized with future sessions
- must preserve the same candle structure as realtime chart data

---

## Future Chart Data Options

### Option A: Add marker to existing chart table

If current chart table is:

```text
trading_chart_candles
```

Add field:

```text
timeline_type: realtime / future
```

Then admin queries:

```text
timeline_type = future
```

Client queries:

```text
timeline_type = realtime
```

### Option B: Create separate future chart table

Recommended if safer.

Create:

```text
admin_future_chart_candles
```

Fields should mirror the existing chart candle table:

- id
- symbol
- interval
- timestamp
- open
- high
- low
- close
- volume
- session_id nullable
- realtime_candle_id nullable
- future_offset_sessions default 10
- created_at
- updated_at

---

# Synchronization Logic

Future sessions and future chart candles must be generated ahead of realtime.

## Required behavior

At any time, the system should maintain:

```text
current realtime session + next 10 future sessions
```

Example:

```text
Realtime:
Session 100 is open

Admin future:
Session 101 future
Session 102 future
...
Session 110 future
```

When realtime moves forward:

```text
Session 100 closes
Session 101 becomes open
```

Admin future system should shift:

```text
Session 111 is generated as new future session
```

---

## Future Session Status Transition

Future session statuses should transition based on server time.

Rules:

- If now < start_time: status = future
- If start_time <= now < end_time: status = open
- If now >= end_time: status = closed

Even though admin sees future sessions, client trading must still only use the current realtime session.

Client must not be able to trade on future sessions.

---

# Admin Session Management Page

Create or update admin page for session management.

Admin must be able to see:

- past sessions
- current open session
- future sessions
- status: future / open / closed
- symbol
- interval
- start_time
- lock_time
- end_time
- open_price
- close_price
- candle timestamp

---

## Admin Filters

Admin page should support filters:

- symbol
- status
- date range
- session type:
  - realtime
  - future
  - all

---

# Admin Dashboard Chart Requirement

On admin dashboard:

- Market Graph should display future chart data.
- It should display chart approximately 10 sessions ahead.
- The chart UI should be similar to the existing trading page.
- This chart is admin-only.

---

## Client Trading Chart Requirement

On client trading page:

- Continue displaying realtime chart only.
- Do not load future chart data.
- Do not expose future session APIs.

---

# API / Route Requirements

## Admin APIs

Suggested routes:

```php
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/sessions', [AdminSessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/{id}', [AdminSessionController::class, 'show'])->name('sessions.show');

    Route::get('/future-chart/candles', [AdminFutureChartController::class, 'candles'])->name('future-chart.candles');
});
```

## Client APIs

Existing client APIs must remain unchanged.

Client chart API must always filter:

```text
timeline_type = realtime
```

or use only realtime table.

Client session API must only return current realtime session.

---

# Background Worker / Command Requirement

Create a command or service that maintains future sessions.

Suggested command:

```bash
php artisan trading:future-sessions-sync
```

Responsibilities:

1. Read current realtime session.
2. Ensure next 10 sessions exist.
3. Generate future chart candle data for those 10 sessions.
4. Update statuses:
   - future
   - open
   - closed
5. Keep future data synchronized with realtime session timing.

---

## Supervisor Requirement

If the command is long-running, run it via Supervisor.

Example:

```ini
[program:tradex-future-session-worker]
command=/usr/bin/php /var/www/tradex/artisan trading:future-sessions-sync
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/tradex/storage/logs/future-session-worker.log
```

If implemented as scheduled command, register it in Laravel scheduler.

---

# Service Layer Requirement

Use service classes.

Suggested services:

```text
app/Services/Trading/FutureSessionService.php
app/Services/Trading/FutureChartService.php
```

## FutureSessionService

Responsibilities:

- get current realtime session
- calculate next 10 sessions
- create missing future sessions
- update future session statuses
- ensure synchronization

## FutureChartService

Responsibilities:

- generate future candles
- align future candles with future sessions
- prevent client access to future candles
- provide candles for admin dashboard

---

# Synchronization Rules

Future session must match realtime session structure.

Each future session must have:

- same symbol
- same interval
- same duration
- same candle structure
- same session timing rules

Only difference:

```text
future session is ahead of realtime session by offset
```

Default offset:

```text
10 sessions
```

Make this configurable.

Suggested config:

```php
'future_session_offset' => 10
```

---

# Security Rules

- Future sessions are admin-only.
- Future chart data is admin-only.
- Client APIs must never return future sessions.
- Client trading must never accept future session IDs.
- Server-side validation must prevent trading on future sessions.
- Do not trust frontend filters.
- Use server time as the source of truth.

---

# Database Safety

Claude must inspect current migrations/models before coding.

Before making schema changes, Claude must report:

1. Current trading_sessions schema
2. Current trading_chart_candles schema
3. Whether reuse or separate table is safer
4. Recommended migration plan

---

# Expected Output From Claude

Claude must work step by step.

Do NOT generate all code at once.

First output:

1. Architecture plan
2. Whether to reuse existing tables or create separate tables
3. Required migrations
4. File structure
5. Risk analysis

After confirmation, generate implementation in this order:

1. Migrations
2. Models
3. Services
4. Commands / Scheduler / Supervisor notes
5. Admin controllers
6. Admin routes
7. Admin Blade views
8. Admin chart JS
9. Client API safety filters
10. Tests or manual test checklist

---

# Important Instructions

This feature must NOT break existing realtime trading.

Client trading page must behave exactly as before.

Future sessions and future chart data are for admin dashboard only.

The system must guarantee:

```text
Client = realtime only
Admin = realtime + future
```

Future session and realtime session must remain synchronized.
