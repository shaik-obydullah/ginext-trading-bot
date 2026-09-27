<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\StrategyController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/market', [MarketController::class, 'index'])->name('market.index');
    Route::get('/market/chart', [MarketController::class, 'chart'])->name('market.chart');
    Route::get('/market/ticker', [MarketController::class, 'ticker'])->name('market.ticker');
    Route::get('/market/indicators', [MarketController::class, 'indicators'])->name('market.indicators');

    Route::get('/portfolios', [PortfolioController::class, 'index'])->name('portfolios.index');
    Route::post('/portfolios', [PortfolioController::class, 'store'])->name('portfolios.store');
    Route::get('/portfolios/{portfolio}', [PortfolioController::class, 'show'])->name('portfolios.show');
    Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
    Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');
    Route::post('/portfolios/{portfolio}/assets', [PortfolioController::class, 'addAsset'])->name('portfolios.assets.store');
    Route::delete('/portfolios/{portfolio}/assets/{asset}', [PortfolioController::class, 'removeAsset'])->name('portfolios.assets.destroy');

    Route::get('/strategies', [StrategyController::class, 'index'])->name('strategies.index');
    Route::post('/strategies', [StrategyController::class, 'store'])->name('strategies.store');
    Route::get('/strategies/{strategy}', [StrategyController::class, 'show'])->name('strategies.show');
    Route::put('/strategies/{strategy}', [StrategyController::class, 'update'])->name('strategies.update');
    Route::delete('/strategies/{strategy}', [StrategyController::class, 'destroy'])->name('strategies.destroy');
    Route::post('/strategies/{strategy}/activate', [StrategyController::class, 'activate'])->name('strategies.activate');
    Route::post('/strategies/{strategy}/deactivate', [StrategyController::class, 'deactivate'])->name('strategies.deactivate');

    Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');
    Route::get('/trades/create', [TradeController::class, 'create'])->name('trades.create');
    Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
    Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
    Route::post('/trades/{trade}/cancel', [TradeController::class, 'cancel'])->name('trades.cancel');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});
