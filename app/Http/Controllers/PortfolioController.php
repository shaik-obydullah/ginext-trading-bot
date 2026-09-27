<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioAsset;
use App\Services\Market\PriceService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(PriceService $prices)
    {
        $portfolios = auth()->user()->portfolios()->with('assets')->get();

        $portfolioValues = [];
        foreach ($portfolios as $portfolio) {
            $total = 0.0;
            foreach ($portfolio->assets as $asset) {
                if ((float) $asset->quantity <= 0) {
                    continue;
                }
                try {
                    $price = $prices->getCurrentPrice($asset->symbol);
                    $total += (float) $asset->quantity * $price;
                    $asset->current_value = (float) $asset->quantity * $price;
                } catch (\Throwable $e) {
                    report($e);
                }
            }
            $portfolioValues[$portfolio->id] = $total;
        }

        return view('portfolios.index', compact('portfolios', 'portfolioValues'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_currency' => ['required', 'string', 'max:10'],
        ]);

        auth()->user()->portfolios()->create($data);

        return redirect()->route('portfolios.index')->with('status', 'Portfolio created.');
    }

    public function show(Portfolio $portfolio, PriceService $prices)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $portfolio->load('assets', 'trades');

        $values = [];
        foreach ($portfolio->assets as $asset) {
            if ((float) $asset->quantity <= 0) {
                continue;
            }
            try {
                $price = $prices->getCurrentPrice($asset->symbol);
                $values[$asset->symbol] = $price;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('portfolios.show', compact('portfolio', 'values'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_currency' => ['required', 'string', 'max:10'],
        ]);

        $portfolio->update($data);

        return redirect()->route('portfolios.show', $portfolio)->with('status', 'Portfolio updated.');
    }

    public function destroy(Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $portfolio->delete();

        return redirect()->route('portfolios.index')->with('status', 'Portfolio deleted.');
    }

    public function addAsset(Request $request, Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $data = $request->validate([
            'symbol' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'avg_buy_price' => ['required', 'numeric', 'min:0'],
        ]);

        PortfolioAsset::updateOrCreate(
            ['portfolio_id' => $portfolio->id, 'symbol' => strtoupper($data['symbol'])],
            [
                'quantity' => $data['quantity'],
                'avg_buy_price' => $data['avg_buy_price'],
            ]
        );

        return redirect()->route('portfolios.show', $portfolio)->with('status', 'Asset added.');
    }

    public function removeAsset(Portfolio $portfolio, PortfolioAsset $asset)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $asset->delete();

        return redirect()->route('portfolios.show', $portfolio)->with('status', 'Asset removed.');
    }
}
