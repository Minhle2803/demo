# FEATURE HISTORY

## 1. Authentication System
- Using client guard
- Login via email or phone
- Custom validation via FormRequest
- ApiResponse + ErrorCodes

---

## 2. Wallet System
- balance field used for trading
- No trading_balance used
- Wallet deduction happens immediately on trade

---

## 3. Session Trading (COMPLETED)
- 60s session:
  - 50s open
  - 10s locked
- User can buy/sell prediction
- 1 user = 1 trade per session
- Result based on candle close_price
- Win = amount * 2
- Synced with trading_chart_candles

---

## 4. Realtime System
- Using Laravel Reverb
- Channels:
  - trading.chart.{symbol}
  - trading.session
  - trading.result.{session_id}

---

## 5. KYC System
- Required for trading
- Fields:
  - kyc_front_url
  - kyc_back_url
- Must be NOT empty to pass verification

---

## 6. Frontend
- Blade + Vite
- JS modular (resources/js)
- Dark UI (Binance style)

---

## IMPORTANT RULES

- Do NOT create new auth system
- Do NOT duplicate wallet logic
- Do NOT break session trading
- Always reuse existing models/services