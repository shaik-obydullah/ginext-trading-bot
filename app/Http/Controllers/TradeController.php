<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Trade;
use App\Services\Trading\OrderService;
use App\Services\Trading\RiskService;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index(Request $request)
    {
        $trades = Trade::where('user_id', auth()->id())
            ->with('portfolio')
            ->when($request->filled('symbol'), fn ($q) => $q->where('symbol', strtoupper($request->symbol)))
            ->when($request->filled('side'), fn ($q) => $q->where('side', $request->side))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20);

        $symbols = Trade::where('user_id', auth()->id())->distinct()->pluck('symbol');

        return view('trades.index', compact('trades', 'symbols'));
    }

    public function create()
    {
        $portfolios = auth()->user()->portfolios;

        return view('trades.create', compact('portfolios'));
    }

    public function store(Request $request, OrderService $orders, RiskService $risk)
    {
        $data = $request->validate([
            'portfolio_id' => ['nullable', 'exists:portfolios,id'],
            'symbol' => ['required', 'string', 'max:20'],
            'side' => ['required', 'in:buy,sell'],
            'type' => ['required', 'in:market,limit'],
            'quantity' => ['required', 'numeric', 'min:0.00000001'],
            'price' => ['nullable', 'required_if:type,limit', 'numeric', 'min:0'],
        ]);

        $portfolio = $data['portfolio_id'] ? Portfolio::find($data['portfolio_id']) : null;
        if ($portfolio && $portfolio->user_id !== auth()->id()) {
            abort(403);
        }

        $data['symbol'] = strtoupper($data['symbol']);

        $check = $risk->check(auth()->id(), (float) $data['quantity'], [
            'exposure' => $portfolio ? $portfolio->assets->sum('quantity') / 100 : 0,
        ]);

        if (! $check['approved']) {
            return back()->withErrors(['quantity' => $check['reason']])->withInput();
        }

        $trade = $orders->create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        $orders->execute($trade);

        return redirect()->route('trades.index')->with('status', 'Trade executed.');
    }

    public function show(Trade $trade)
    {
        abort_unless($trade->user_id === auth()->id(), 403);

        $trade->load('orders', 'portfolio', 'strategy');

        return view('trades.show', compact('trade'));
    }

    public function cancel(Trade $trade, OrderService $orders)
    {
        abort_unless($trade->user_id === auth()->id(), 403);

        if (in_array($trade->status, ['filled', 'cancelled', 'failed'])) {
            return back()->with('error', 'Trade cannot be cancelled.');
        }

        $orders->cancel($trade);

        return back()->with('status', 'Trade cancelled.');
    }
}
