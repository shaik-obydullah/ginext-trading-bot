# AGENTS.md

Guidance for AI agents (and human devs) working in this repository. Read this first.

## What this project is

**Ginext Trading Bot** — an automated cryptocurrency trading platform built on **Laravel 13 / PHP 8.4**. It fetches real market data from the Binance public API, runs algorithmic strategies (RSI, MACD, Grid, DCA), enforces risk rules on every trade, manages multi-asset portfolios, and exposes a Sanctum-secured REST API. Frontend is Blade + Alpine.js + Tailwind CSS 4 + Chart.js, compiled with Vite.

It is NOT a trading-service mock. It performs live order execution against Binance prices. Be careful with anything that sends real orders.

## Stack (verified working)

| Layer | Tech |
|---|---|
| Backend | Laravel 13.23, PHP 8.4 (Docker image `php:8.4-fpm`) |
| Frontend | Blade, Alpine.js, Tailwind CSS 4, Chart.js, Vite 8 |
| Database | MySQL 8.0 (primary), Redis 7 (queue/cache) |
| Auth | Laravel Sanctum (API tokens + stateful web) |
| Queue | Redis-backed; jobs dispatched synchronously via `dispatchSync` in the scheduled commands |
| Tests | PHPUnit, in-memory SQLite via `RefreshDatabase` |

## How to run it

Docker Compose is the source of truth. Local PHP is NOT required (and not installed on the host).

```bash
docker compose up -d                # web:8051, phpMyAdmin:8052
docker compose exec app php artisan migrate --seed
docker compose exec app npm run build   # first time only
docker compose exec app php artisan schedule:work   # keep prices fresh
```

- App: http://localhost:8051 — Demo login: `admin@example.com` / `password`
- phpMyAdmin: http://localhost:8052 — root / root_secret
- DB credentials and the `DB_*` env vars are injected by `docker-compose.yml` (see "Gotchas").

## Key commands

```bash
docker compose exec app php artisan trading:update-prices   # fetch prices + indicators for active strategies
docker compose exec app php artisan trading:check-stop-loss  # evaluate pending stop-loss orders
docker compose exec app php artisan test                    # run the PHPUnit suite
docker compose exec app php artisan migrate:status           # check migration state
```

Both `trading:*` commands are scheduled every minute in `routes/console.php`.

## Architecture

```
routes/
  web.php        # Blade UI routes (all auth-protected except /login, /register)
  api.php        # Sanctum-secured JSON API (post /api/login, then Bearer token)
  console.php    # artisan commands + schedule
app/
  Http/Controllers/
    AuthController, DashboardController, MarketController, PortfolioController,
    StrategyController, TradeController, AnalyticsController     # web
    Api/ Auth, Portfolio, Strategy, Trade, Market, Analytics     # JSON API
  Models/          # 15 Eloquent models matching the tables below
  Services/
    Exchange/    ExchangeInterface + BinanceService (live API calls)
    Strategy/    StrategyInterface, StrategyEngine, Rsi/Macd/Grid/Dca strategies
    Trading/     OrderService, PositionService, RiskService
    Analytics/   IndicatorService, PerformanceService
    Market/      PriceService
  Jobs/          UpdatePrices, CalculateIndicators, ExecuteTrade, CheckStopLoss
bootstrap/app.php   # Laravel 13 app bootstrap, Sanctum middleware, API JSON errors
```

### Core flow

1. `trading:update-prices` dispatches `UpdatePrices` (fetch OHLCV/klines from Binance for each active strategy symbol, persist to `price_history`) then `CalculateIndicators` (RSI/SMA/MACD/Bollinger into `indicators`).
2. `StrategyEngine::evaluate()` feeds candles to each active `StrategyInterface` implementation and returns a buy/sell/hold signal.
3. `RiskService::checkRiskLimits()` validates max position size, daily loss limit, max open orders, and max exposure against `risk_rules` before an order is accepted.
4. `OrderService` executes market/limit orders at live prices; `PositionService` updates portfolio asset quantity / average buy price / fees on fill.
5. `trading:check-stop-loss` dispatches `CheckStopLoss` to monitor pending stop-loss orders.

## Database (19 migrations under `database/migrations`)

`users`, `api_keys` (encrypted credentials), `portfolios`, `portfolio_assets`, `strategies`, `trades`, `orders`, `price_history`, `indicators`, `risk_rules`, `stop_loss_orders`, `daily_performance`, `trade_history`, `reports`, `audit_logs`, plus framework tables (cache, jobs, personal_access_tokens, sessions).

Convention: namespaced timestamps `2026_08_01_*` for app tables. `parameters_json` is JSON-cast on `Strategy`/`RiskRule`.

## Conventions

- **PHP 8.4 + strict typing** — typed properties, return types; no docblock-only typing.
- **Controllers stay thin**; business logic goes in the `app/Services/*` layer.
- **Laravel 13 style** — `bootstrap/app.php` for app config (no old `Kernel.php`).
- **No `app/Enums`, `app/Notifications`, `app/Events` directories exist** — don't assume them.
- **No `app/Http/Requests` directory** — validation is currently inline in controllers.
- No code comments are used in source files; keep new code comment-free and self-explanatory.
- Follow existing naming (`XxxService`, `XxxStrategy`, job verbs like `UpdatePrices`).

## Testing

- `tests/TestCase.php` forces `database.default => sqlite` and `database.connections.sqlite.database => ':memory:'` in `setUp()`, and applies `RefreshDatabase`. All feature tests get a fresh migrated DB automatically.
- Run: `docker compose exec app php artisan test`.
- Currently: `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php` (guest redirect + authenticated dashboard access). Extend these patterns for new features.

## Gotchas (learned the hard way)

1. **The Docker Compose environment overrides `.env`.** The `app` service sets real `DB_DATABASE=trading`, `DB_HOST=db`, etc. as container env vars. PHPUnit's `<env>` entries do NOT override real env vars, so the sqlite override MUST happen in `tests/TestCase.php::setUp()` (via `config([...])`), not in `phpunit.xml`. Do not remove that config block.
2. **`php artisan test` runs inside the container** — the container env vars are active. Always run tests with `docker compose exec app php artisan test`.
3. **Migrations were already run** against the `trading` database in the Docker volume. `php artisan migrate` reports "Nothing to migrate". If you change a migration, `docker compose down -v` first (deletes the MySQL volume), then `up` + `migrate --seed`.
4. **`restart: "no"`** on all containers — they do not auto-restart after a host reboot or crash. The web server answers on port **8051** (not 80).
5. **`dispatchSync`** is used everywhere (sync queue) — jobs run inline in the request/command; there is no worker container.
6. **Frontend assets are pre-built** in `public/build`. After editing `resources/views` or `resources/css`/`resources/js`, run `docker compose exec app npm run build` (the container has Node 22).
7. `.env` exists locally with an APP_KEY already generated. Never commit secrets or real exchange API keys.
8. Binance calls are live HTTP requests to the public API. Tests and local runs that don't need data should not hammer the API.

## When making changes

- After changes, run: `docker compose exec app php artisan test` and, if you touched frontend, `docker compose exec app npm run build`.
- If you add a model, add the migration + factory + a feature test.
- Update this file when conventions or the architecture meaningfully change.
