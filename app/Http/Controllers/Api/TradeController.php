<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            ->with('portfolio', 'strategy')
            ->when($request->filled('symbol'), fn ($q) => $q->where('symbol', strtoupper($request->symbol)))
            ->when($request->filled('side'), fn ($q) => $q->where('side', $request->side))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json($trades);
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

        $portfolio = $data['portfolio_id'] ?? null
            ? Portfolio::find($data['portfolio_id'])
            : null;

        if ($portfolio && $portfolio->user_id !== auth()->id()) {
            abort(403);
        }

        $data['symbol'] = strtoupper($data['symbol']);

        $check = $risk->check(auth()->id(), (float) $data['quantity'], [
            'exposure' => $portfolio ? $portfolio->assets->sum('quantity') / 100 : 0,
        ]);

        if (! $check['approved']) {
            return response()->json(['message' => $check['reason']], 422);
        }

        $trade = $orders->create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        $orders->execute($trade);

        return response()->json($trade, 201);
    }

    public function show(Trade $trade)
    {
        abort_unless($trade->user_id === auth()->id(), 403);

        return response()->json($trade->load('orders', 'portfolio', 'strategy'));
    }

    public function history(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $trades = Trade::where('user_id', auth()->id())
            ->where('status', 'filled')
            ->when($from, fn ($q) => $q->where('executed_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('executed_at', '<=', $to))
            ->orderByDesc('executed_at')
            ->get();

        return response()->json($trades);
    }
}
