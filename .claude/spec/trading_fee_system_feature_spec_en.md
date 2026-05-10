# Feature Specification: Trading Fee System Integration

## Overview

Implement a trading fee system for the existing realtime trading feature.

This feature must integrate into the current trading system without breaking existing trading logic.

---

## Existing Related Files

### Controller

```text
app/Http/Controllers/Trade/TradingSessionController.php
```

---

### JavaScript

```text
resources/js/trading-session
```

---

### Trading View

```text
resources/views/pages/tradding.blade.php
```

Important component:

```text
tradeConfirmModal
```

This modal is used to confirm trading orders before submission.

---

# New Requirement

A configurable trading fee must be added to the trading system.

The trading fee value will be configured from the admin dashboard settings page.

---

## Admin Setting Location

Trading fee setting must be managed from:

```text
resources/views/pages/admin/settings
```

Suggested setting key:

```text
trading_fee_percent
```

or:

```text
trading_fee_amount
```

Claude must inspect the existing settings system before implementation.

If the project already has a `project_settings` table or similar setting storage system, reuse it.

---

# Trading Fee Logic

## Core Rules

### If user wins

The amount received must be:

```text
received_amount = bet_amount - trading_fee
```

Example:

```text
Bet amount: 100
Trading fee: 5
Winning payout: 95
```

---

### If user loses

No trading fee is charged.

Example:

```text
Bet amount: 100
User loses
Fee charged: 0
```

---

# Trading Confirmation Modal

## File

```text
resources/views/pages/tradding.blade.php
```

Component:

```text
tradeConfirmModal
```

---

## Requirement

When user submits a trade:

- show confirmation popup/modal
- display trading fee amount
- display estimated received amount if win
- display final payout calculation clearly

---

## Required Information in Modal

The modal must display:

- trade type (buy/sell)
- session information
- bet amount
- trading fee
- estimated payout if win
- warning/confirmation text

---

## Example Display

```text
Trade Amount: 100 USDT
Trading Fee: 5 USDT
Estimated Win Amount: 95 USDT
```

---

# Trading Table Update

## File

```text
resources/views/pages/tradding.blade.php
```

Table:

```text
#tradesTable
```

---

## Requirement

Add a new column:

```text
Trading Fee
```

The user must be able to see:

- fee amount per trade
- final payout
- win/lose status

---

# Database Requirement

The `trades` table must store the trading fee.

Suggested new field:

```text
trading_fee
```

Suggested type:

```php
decimal(18, 2)
```

Claude must inspect the existing `trades` table migration before coding.

---

# Admin Trade Management Update

Admin trade pages must also display trading fees.

---

## Session Detail Page

### File

```text
resources/views/pages/admin/sessions/show.blade.php
```

---

## Requirement

Display:

- trading fee per trade
- total trading fee collected in session
- payout information
- trade amount
- trade result

---

# Admin Dashboard Update

## File

```text
resources/views/pages/admin/dashboard
```

---

## Requirement

Dashboard must display:

```text
Total Trading Fees Collected
```

This value should be calculated from all completed trades.

---

# Frontend JavaScript Requirement

## File

```text
resources/js/trading-session
```

---

## Requirement

JavaScript must:

1. Load trading fee configuration
2. Calculate estimated payout
3. Update confirmation modal dynamically
4. Display fee information before order confirmation

---

## Important

Do not hardcode fee values in JavaScript.

Fee configuration must come from backend/API/settings.

---

# Backend Service Requirement

Suggested service:

```text
app/Services/Trading/TradingFeeService.php
```

Responsibilities:

- get current fee configuration
- calculate fee
- calculate final payout
- centralize fee logic
- prevent duplicated calculations

---

# Security Rules

- Fee calculation must happen on backend
- Frontend calculations are display-only
- Do not trust frontend fee values
- Prevent client manipulation
- Store actual fee value in database
- Fee settings should be admin-only

---

# Expected Output From Claude

Claude must work step by step.

Do NOT generate all code at once.

First output:

1. Architecture plan
2. Existing payout logic analysis
3. Database changes required
4. Fee logic recommendation
5. File structure

After confirmation, generate implementation in this order:

1. Migration
2. Settings integration
3. TradingFeeService
4. Controller updates
5. Trade settlement updates
6. Blade modal updates
7. Trading table updates
8. Admin dashboard updates
9. Admin session detail updates
10. JS updates
11. Lang/messages updates
