# Ginext Trading Bot

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/Redis-7-DC382D?style=for-the-badge&logo=redis&logoColor=white" alt="Redis 7">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge" alt="MIT License">
</p>

Automated crypto trading bot, built for Binance automated trading with real-time market data, multiple strategies, portfolio management, risk controls, and analytics.

## Features

- **Real Binance data** — live prices, 24h tickers, and OHLCV klines from the Binance public API
- **Trading strategies** — RSI Crossover, MACD Crossover, Grid Trading, and DCA
- **Strategy engine** — evaluates live candles and emits buy/sell/hold signals
- **Portfolio management** — multi-asset holdings, live valuation, avg buy price tracking
- **Risk management** — max position size, daily loss limit, max open orders, exposure limits
- **Trade execution** — market/limit orders priced at real exchange rates, position auto-update
- **Analytics** — RSI, SMA, MACD, Bollinger Bands, win rate, P&L
- **Real-time dashboard** — live price charts (Chart.js + Alpine.js)
- **REST API** — Sanctum token auth, portfolios, strategies, trades, market, analytics
- **Scheduler** — `trading:update-prices` and `trading:check-stop-loss` commands

## Tech Stack

| Layer      | Technology                                                                |
| ---------- | ------------------------------------------------------------------------- |
| Backend    | Laravel 13, PHP 8.4                                                       |
| Frontend   | Blade, Alpine.js, Tailwind CSS 4, Chart.js, Vite                          |
| Database   | MySQL 8.0 + Redis 7                                                       |
| Auth       | Laravel Sanctum                                                           |
| Queue/Jobs | Redis-backed queue, scheduled jobs                                        |
| Docker     | Docker + Docker Compose (containers prefixed `trading_`, no auto-restart) |

## Docker Setup

Containers are prefixed `trading_` and use `restart: "no"`.

| Service     | Container            | Port            |
| ----------- | -------------------- | --------------- |
| Web (Nginx) | `trading_web`        | **8051**        |
| PHP-FPM     | `trading_app`        | 9000            |
| MySQL       | `trading_db`         | 3306 (internal) |
| Redis       | `trading_redis`      | 6379 (internal) |
| phpMyAdmin  | `trading_phpmyadmin` | **8052**        |

## Installation

```bash
docker compose up -d

docker compose exec -u appuser app php artisan migrate --seed
docker compose exec -u appuser app php artisan key:generate

# (first time only) build frontend assets
docker compose exec -u appuser app npm install
docker compose exec -u appuser app npm run build

# keep prices fresh
docker compose exec -u appuser app php artisan schedule:work
```

- App: **http://localhost:8051**
- phpMyAdmin: **http://localhost:8052** (root / root_secret)

### Demo login

```
admin@example.com / password
```

## Usage

- **Dashboard** — live market prices, portfolio value, recent trades
- **Market** — interactive price chart with symbol/interval switching
- **Portfolios** — add assets, track live value and average buy price
- **Strategies** — configure and activate bots; live signals shown
- **Trades** — place market/limit orders at real prices; risk checks applied
- **Analytics** — win rate, P&L, RSI/MACD/SMA/Bollinger indicators

## Screenshots

| | |
| --- | --- |
| **Login** | ![Login](screenshots/Login.png) |
| **Bot Dashboard** | ![Bot Dashboard](screenshots/Bot-Dashboard.png) |
| **Market Trend** | ![Market Trend](screenshots/Market-Trend.png) |
| **Portfolio Creation** | ![Portfolio Creation](screenshots/Portfolio-Creation.png) |
| **New Trade** | ![New Trade](screenshots/New-Trade.png) |
| **Strategies** | ![Strategies](screenshots/Strategies.png) |
| **Trade History** | ![Trade History](screenshots/Trade-History.png) |
| **Analytics** | ![Analytics](screenshots/Analytics.png) |

### Manual commands

```bash
# Update prices + indicators for all active strategy symbols
docker compose exec -u appuser app php artisan trading:update-prices

# Evaluate pending stop losses
docker compose exec -u appuser app php artisan trading:check-stop-loss
```

## API (Sanctum)

```bash
TOKEN=$(curl -s -X POST http://localhost:8051/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' | jq -r .token)

curl -H "Authorization: Bearer $TOKEN" http://localhost:8051/api/portfolios
curl -H "Authorization: Bearer $TOKEN" http://localhost:8051/api/market/prices/BTCUSDT
```

Endpoints: `/api/login`, `/api/user`, `/api/portfolios`, `/api/strategies`, `/api/trades`, `/api/market/{prices,history,indicators,ticker}`, `/api/analytics/*`.

## Project Structure

```
app/
├── Http/Controllers/        # Web + Api controllers
├── Models/                  # Eloquent models
├── Services/
│   ├── Exchange/            # BinanceService (real API integration)
│   ├── Strategy/            # RSI, MACD, Grid, DCA + engine
│   ├── Trading/             # Order, Position, Risk services
│   ├── Analytics/           # Indicators, Performance
│   └── Market/              # PriceService
└── Jobs/                    # UpdatePrices, CalculateIndicators, ExecuteTrade, CheckStopLoss
```

## License

MIT
