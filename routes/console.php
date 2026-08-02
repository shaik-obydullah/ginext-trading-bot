<?php

use App\Jobs\CalculateIndicators;
use App\Jobs\CheckStopLoss;
use App\Jobs\UpdatePrices;
use App\Models\Strategy;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('trading:update-prices', function () {
    $symbols = Strategy::where('status', 'active')
        ->pluck('symbol')
        ->filter()
        ->unique()
        ->values()
        ->all();

    if (empty($symbols)) {
        $symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT'];
    }

    UpdatePrices::dispatchSync($symbols);
    CalculateIndicators::dispatchSync($symbols);
    $this->info('Prices updated for: '.implode(', ', $symbols));
})->purpose('Fetch latest prices from Binance and recalculate indicators');

Artisan::command('trading:check-stop-loss', function () {
    CheckStopLoss::dispatchSync();
    $this->info('Stop loss check complete.');
})->purpose('Evaluate pending stop loss orders');

Schedule::command('trading:update-prices')->everyMinute();
Schedule::command('trading:check-stop-loss')->everyMinute();
