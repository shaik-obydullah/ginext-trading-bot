# Ginext Trading Bot - Laravel 13 Upgrade Plan

## Overview

Upgrade of the existing Ginext (onetapexc) trading bot from Laravel 7 to Laravel 13 with modern architecture, enhanced security, and additional features. Originally built for Binance automated trading.

**Original:** https://github.com/skarnov/onetapexc (Laravel 7.x)
**New:** Laravel 13 + Blade + Alpine.js + Tailwind CSS 4

---

## Why This Project Matters

| Aspect | Impact |
|--------|--------|
| Industry | FinTech / Crypto Trading |
| Complexity | High (trading algorithms, API integration) |
| Real-world | Binance integration, live trading |
| Skills | Algorithmic trading, API integration, real-time data |

---

## Current Features (Laravel 7)

| Feature | Status |
|---------|--------|
| Automated Binance trading | ✅ |
| Multiple trading strategies | ✅ |
| User dashboard | ✅ |
| Trade monitoring | ✅ |
| Profit reporting | ✅ |
| Notification system | ✅ |
| Binance API integration | ✅ |
| Tron API integration | ✅ |

---

## New Features (Laravel 13)

### 1. Enhanced Security
- Sanctum API authentication
- Encrypted API keys storage
- Rate limiting
- CSRF protection
- Input validation

### 2. Advanced Trading Features
- Backtesting engine
- Paper trading mode
- Multiple exchange support
- Custom strategy builder
- Risk management tools

### 3. Real-time Dashboard
- Live price charts (TradingView)
- Real-time P&L updates
- WebSocket connections
- Portfolio visualization

### 4. Portfolio Management
- Multi-portfolio support
- Asset allocation tracking
- Performance metrics
- Historical analysis

### 5. Advanced Analytics
- Technical indicators (RSI, MACD, Bollinger)
- Market sentiment analysis
- correlation analysis
- Risk metrics

### 6. Automated Reports
- Daily/weekly/monthly reports
- Tax reporting
- Email notifications
- PDF exports

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Blade + Alpine.js + Tailwind CSS 4 |
| Database | MySQL 8.0 + Redis |
| Auth | Laravel Sanctum |
| Real-time | Laravel WebSockets |
| Queue | Redis (trade execution) |
| Charts | TradingView Widget |
| Exports | Laravel Excel / DomPDF |
| Docker | Docker + Docker Compose |
| Testing | PHPUnit + Pest |
| CI/CD | GitHub Actions |

---

## Database Schema

### Core Tables (25+ tables)

#### Users & Auth
```sql
users (id, name, email, password, status, timestamps)

api_keys (id, user_id, exchange, api_key_encrypted, 
          api_secret_encrypted, status, timestamps)
```

#### Portfolios
```sql
portfolios (id, user_id, name, description, 
            default_currency, status, timestamps)

portfolio_assets (id, portfolio_id, symbol, quantity, 
                  avg_buy_price, current_value, timestamps)
```

#### Trading
```sql
strategies (id, user_id, name, type, parameters_json,
            status, timestamps)

trades (id, portfolio_id, strategy_id, exchange, symbol,
        side, type, quantity, price, total, fee,
        status, executed_at, timestamps)

orders (id, trade_id, exchange_order_id, type, status,
        filled_quantity, remaining_quantity, timestamps)
```

#### Market Data
```sql
price_history (id, symbol, exchange, open, high, low, close,
               volume, timestamp, timestamps)

indicators (id, symbol, indicator_type, value, period,
            calculated_at, timestamps)
```

#### Risk Management
```sql
risk_rules (id, user_id, rule_type, parameters_json,
            status, timestamps)

stop_loss_orders (id, trade_id, trigger_price, status,
                  executed_at, timestamps)
```

#### Analytics
```sql
daily_performance (id, portfolio_id, date, start_value,
                   end_value, pnl, pnl_percentage, timestamps)

trade_history (id, portfolio_id, period_start, period_end,
               total_trades, winning_trades, losing_trades,
               total_pnl, timestamps)
```

#### Reports
```sql
reports (id, user_id, type, title, parameters_json,
         file_path, generated_at, timestamps)
```

#### Audit
```sql
audit_logs (id, user_id, action, subject_type, subject_id,
            old_values, new_values, ip_address, timestamps)
```

---

## Trading Strategies

### Built-in Strategies
| Strategy | Description |
|----------|-------------|
| **DCA** | Dollar Cost Averaging |
| **Grid Trading** | Grid bot with configurable ranges |
| **RSI Crossover** | RSI overbought/oversold signals |
| **MACD Crossover** | MACD signal line crossover |
| **Bollinger Band** | Mean reversion strategy |
| **Moving Average** | SMA/EMA crossover |
| **Custom** | User-defined rules |

### Strategy Parameters
```json
{
  "name": "RSI Crossover",
  "symbol": "BTCUSDT",
  "timeframe": "1h",
  "rsi_period": 14,
  "oversold": 30,
  "overbought": 70,
  "position_size": 0.01,
  "stop_loss": 2.0,
  "take_profit": 5.0
}
```

---

## API Endpoints

### Auth
```
POST   /api/login
POST   /api/logout
GET    /api/user
```

### Portfolios
```
GET    /api/portfolios
POST   /api/portfolios
GET    /api/portfolios/{id}
PUT    /api/portfolios/{id}
DELETE /api/portfolios/{id}
GET    /api/portfolios/{id}/assets
GET    /api/portfolios/{id}/performance
```

### Trading
```
GET    /api/strategies
POST   /api/strategies
GET    /api/strategies/{id}
PUT    /api/strategies/{id}
POST   /api/strategies/{id}/activate
POST   /api/strategies/{id}/deactivate

GET    /api/trades
POST   /api/trades
GET    /api/trades/{id}
GET    /api/trades/history
```

### Market Data
```
GET    /api/market/prices/{symbol}
GET    /api/market/history/{symbol}
GET    /api/market/indicators/{symbol}
GET    /api/market/ticker
```

### Analytics
```
GET    /api/analytics/performance
GET    /api/analytics/trades
GET    /api/analytics/risk
GET    /api/analytics/compare
```

### Reports
```
GET    /api/reports
POST   /api/reports/generate
GET    /api/reports/{id}
GET    /api/reports/{id}/download
```

---

## Project Structure

```
ginext-trading-bot/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── PortfolioController.php
│   │   │   │   ├── StrategyController.php
│   │   │   │   ├── TradeController.php
│   │   │   │   ├── MarketController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   └── ReportController.php
│   │   │   └── Web/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   │   ├── Exchange/
│   │   │   ├── BinanceService.php
│   │   │   ├── KrakenService.php
│   │   │   └── ExchangeInterface.php
│   │   ├── Strategy/
│   │   │   ├── RsiStrategy.php
│   │   │   ├── MacdStrategy.php
│   │   │   ├── GridStrategy.php
│   │   │   ├── DcaStrategy.php
│   │   │   └── StrategyInterface.php
│   │   ├── Trading/
│   │   │   ├── OrderService.php
│   │   │   ├── PositionService.php
│   │   │   └── RiskService.php
│   │   ├── Analytics/
│   │   │   ├── PerformanceService.php
│   │   │   ├── IndicatorService.php
│   │   │   └── ReportService.php
│   │   └── Market/
│   │       ├── PriceService.php
│   │       └── WebSocketService.php
│   ├── Jobs/
│   │   ├── ExecuteTrade.php
│   │   ├── UpdatePrices.php
│   │   ├── CalculateIndicators.php
│   │   ├── GenerateReport.php
│   │   └── CheckStopLoss.php
│   ├── Notifications/
│   └── Enums/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── dashboard/
│       ├── portfolios/
│       ├── strategies/
│       ├── trades/
│       ├── analytics/
│       └── reports/
├── routes/
├── tests/
├── docker/
├── docker-compose.yml
├── README.md
└── .github/
    └── workflows/
        └── ci.yml
```

---

## Key Business Logic

### Trade Execution Flow
```
1. Strategy triggers signal (e.g., RSI oversold)
2. Risk management check (position size, limits)
3. Create order in database (pending)
4. Execute order via exchange API
5. Update order status (filled/partial/failed)
6. Update portfolio assets
7. Calculate fees
8. Log trade
9. Send notification
10. Update analytics
```

### Risk Management
```
function checkRiskLimits(trade):
    // 1. Max position size
    if trade.amount > max_position_size:
        reject("Exceeds max position")
    
    // 2. Daily loss limit
    if daily_loss > daily_loss_limit:
        reject("Daily loss limit reached")
    
    // 3. Max open orders
    if open_orders >= max_open_orders:
        reject("Max open orders reached")
    
    // 4. Exposure limit
    if portfolio_exposure > max_exposure:
        reject("Portfolio exposure limit")
    
    return approve
```

### Indicator Calculation
```
function calculateRSI(prices, period=14):
    gains = []
    losses = []
    
    for i in 1..period:
        change = prices[i] - prices[i-1]
        if change > 0:
            gains.push(change)
        else:
            losses.push(abs(change))
    
    avg_gain = gains.sum() / period
    avg_loss = losses.sum() / period
    
    rs = avg_gain / avg_loss
    rsi = 100 - (100 / (1 + rs))
    
    return rsi
```

---

## Development Timeline: 2.5 Weeks

### Week 1: Foundation
- Day 1-2: Laravel 13 setup, Auth, Sanctum
- Day 3: Portfolio management
- Day 4: Exchange service (Binance)
- Day 5: Basic trade execution

### Week 2: Core Features
- Day 6-7: Strategy engine + 3 built-in strategies
- Day 8: Risk management
- Day 9: Real-time price updates
- Day 10: Analytics & indicators

### Week 3: Polish
- Day 11: Dashboard with charts
- Day 12: Reports & exports
- Day 13: Testing (Unit + Feature)
- Day 14: README with screenshots
- Day 15-17: Final testing & deployment

---

## README Highlights

### Badges
- Laravel 13
- PHP 8.3
- MySQL 8.0
- Docker
- PHPUnit

### Features
- Automated Binance trading
- Multiple strategies (RSI, MACD, Grid, DCA)
- Real-time dashboard
- Portfolio management
- Risk management
- Backtesting
- Paper trading
- Dockerized setup

### Installation
```bash
git clone https://github.com/shaik-obydullah/ginext-trading-bot.git
cd ginext-trading-bot
composer install
cp .env.example .env
php artisan key:generate
docker-compose up -d
php artisan migrate --seed
php artisan serve
```

### Screenshots
- Dashboard with live prices
- Strategy configuration
- Trade history
- Performance charts

---

## Portfolio Value

### After This Addition (9 Laravel Projects)
```
Healthcare + Business + Data + Real Estate + Government + 
FinTech (Settlement) + Network Marketing + NGO + CRYPTO TRADING
= "Senior Laravel developer with extensive industry experience across 9 sectors"
```

### Interview Story
> "I upgraded a crypto trading bot from Laravel 7 to Laravel 13. It supports automated Binance trading with multiple strategies like RSI, MACD, and Grid trading. The system handles real-time price updates, risk management, and portfolio analytics."

### Skills Demonstrated
| Skill | How It's Shown |
|-------|----------------|
| Laravel 13 | Modern framework features |
| API Integration | Binance + Tron APIs |
| Algorithmic Trading | Strategy engine |
| Real-time Data | WebSocket connections |
| Queue Jobs | Background trade execution |
| Risk Management | Position sizing, limits |
| Analytics | Technical indicators |
| Docker | Production-ready setup |

---

## Comparison: Old vs New

| Aspect | Old (Laravel 7) | New (Laravel 13) |
|--------|-----------------|------------------|
| Framework | Laravel 7 | Laravel 13 |
| Auth | Basic | Sanctum + 2FA |
| Exchange | Binance only | Multi-exchange |
| Strategies | Basic | Advanced + Custom |
| Real-time | Polling | WebSocket |
| Risk | None | Full risk engine |
| Analytics | Basic | Technical indicators |
| Testing | None | PHPUnit + Pest |
| Security | Basic | Production-ready |

---

## Ready to Build?

When ready, run:
```bash
# Create Laravel 13 project
composer create-project laravel/laravel ginext-trading-bot

# Install dependencies
composer require laravel/sanctum
composer require laravel/websockets
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

# Install Tailwind CSS + Alpine.js
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```
