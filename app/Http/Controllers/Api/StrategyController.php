<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Strategy;
use App\Services\Strategy\StrategyEngine;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function index(Request $request, \App\Services\Market\PriceService $prices)
    {
        $strategies = auth()->user()->strategies()->get();

        if ($request->expectsJson()) {
            return response()->json($strategies);
        }

        $engine = app(StrategyEngine::class);

        $signals = [];
        foreach ($strategies as $strategy) {
            if ($strategy->status !== 'active' || ! $strategy->symbol) {
                $signals[$strategy->id] = null;
                continue;
            }
            try {
                $candles = $prices->history($strategy->symbol, $strategy->timeframe, 60);
                $signals[$strategy->id] = $engine->evaluate($strategy, $candles);
            } catch (\Throwable $e) {
                report($e);
                $signals[$strategy->id] = null;
            }
        }

        return view('strategies.index', compact('strategies', 'signals'));
    }

    public function store(Request $request, StrategyEngine $engine)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:rsi_crossover,macd_crossover,grid,dca'],
            'symbol' => ['nullable', 'string', 'max:20'],
            'timeframe' => ['required', 'in:5m,15m,30m,1h,4h,1d'],
        ]);

        $strategy = new Strategy($data);
        $strategy->user_id = auth()->id();
        $strategy->parameters_json = $engine->resolve($data['type'])->validateParams($request->all());
        $strategy->status = 'inactive';
        $strategy->save();

        return response()->json($strategy, 201);
    }

    public function show(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        return response()->json($strategy);
    }

    public function update(Request $request, Strategy $strategy, StrategyEngine $engine)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:20'],
            'timeframe' => ['sometimes', 'in:5m,15m,30m,1h,4h,1d'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $strategy->fill($data);
        if (isset($data['symbol']) && $data['symbol']) {
            $strategy->symbol = strtoupper($data['symbol']);
        }
        $strategy->parameters_json = $engine->resolve($strategy->type)->validateParams($request->all());
        $strategy->save();

        return response()->json($strategy);
    }

    public function destroy(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $strategy->delete();

        return response()->json(['message' => 'Strategy deleted.']);
    }

    public function activate(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $strategy->update(['status' => 'active']);

        return response()->json($strategy);
    }

    public function deactivate(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $strategy->update(['status' => 'inactive']);

        return response()->json($strategy);
    }
}
