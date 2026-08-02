<?php

namespace App\Http\Controllers;

use App\Models\Strategy;
use App\Services\Analytics\IndicatorService;
use App\Services\Market\PriceService;
use App\Services\Strategy\StrategyEngine;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function index(PriceService $prices, StrategyEngine $engine, IndicatorService $indicators)
    {
        $strategies = auth()->user()->strategies()->get();

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

        return redirect()->route('strategies.index')->with('status', 'Strategy created.');
    }

    public function show(Strategy $strategy, PriceService $prices, StrategyEngine $engine)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $candles = [];
        $signal = null;
        $rsi = [];

        if ($strategy->symbol) {
            $candles = $prices->history($strategy->symbol, $strategy->timeframe, 60);
            $signal = $engine->evaluate($strategy, $candles);

            if ($strategy->type === 'rsi_crossover') {
                $period = $strategy->parameters_json['rsi_period'] ?? 14;
                $rsi = app(IndicatorService::class)->rsi(array_column($candles, 'close'), $period);
            }
        }

        return view('strategies.show', compact('strategy', 'candles', 'signal', 'rsi'));
    }

    public function update(Request $request, Strategy $strategy, StrategyEngine $engine)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:20'],
            'timeframe' => ['required', 'in:5m,15m,30m,1h,4h,1d'],
        ]);

        $strategy->name = $data['name'];
        $strategy->symbol = $data['symbol'] ? strtoupper($data['symbol']) : null;
        $strategy->timeframe = $data['timeframe'];
        $strategy->parameters_json = $engine->resolve($strategy->type)->validateParams($request->all());
        $strategy->save();

        return redirect()->route('strategies.show', $strategy)->with('status', 'Strategy updated.');
    }

    public function destroy(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $strategy->delete();

        return redirect()->route('strategies.index')->with('status', 'Strategy deleted.');
    }

    public function activate(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $strategy->update(['status' => 'active']);

        return back()->with('status', 'Strategy activated.');
    }

    public function deactivate(Strategy $strategy)
    {
        abort_unless($strategy->user_id === auth()->id(), 403);

        $strategy->update(['status' => 'inactive']);

        return back()->with('status', 'Strategy deactivated.');
    }
}
