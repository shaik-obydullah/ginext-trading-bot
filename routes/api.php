<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\StrategyController;
use App\Http\Controllers\Api\TradeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('portfolios', PortfolioController::class);
    Route::get('portfolios/{portfolio}/assets', [PortfolioController::class, 'assets']);
    Route::get('portfolios/{portfolio}/performance', [PortfolioController::class, 'performance']);

    Route::apiResource('strategies', StrategyController::class);
    Route::post('strategies/{strategy}/activate', [StrategyController::class, 'activate']);
    Route::post('strategies/{strategy}/deactivate', [StrategyController::class, 'deactivate']);

    Route::get('trades', [TradeController::class, 'index']);
    Route::post('trades', [TradeController::class, 'store']);
    Route::get('trades/history', [TradeController::class, 'history']);
    Route::get('trades/{trade}', [TradeController::class, 'show']);

    Route::get('market/prices/{symbol}', [MarketController::class, 'prices']);
    Route::get('market/history/{symbol}', [MarketController::class, 'history']);
    Route::get('market/indicators/{symbol}', [MarketController::class, 'indicators']);
    Route::get('market/ticker', [MarketController::class, 'ticker']);

    Route::get('analytics/performance', [AnalyticsController::class, 'performance']);
    Route::get('analytics/trades', [AnalyticsController::class, 'trades']);
    Route::get('analytics/risk', [AnalyticsController::class, 'risk']);
    Route::get('analytics/compare', [AnalyticsController::class, 'compare']);
});
