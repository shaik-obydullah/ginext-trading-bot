<?php

namespace App\Services\Strategy;

interface StrategyInterface
{
    public function name(): string;

    public function type(): string;

    public function shouldBuy(array $params, array $candles): bool;

    public function shouldSell(array $params, array $candles): bool;

    public function validateParams(array $params): array;
}
