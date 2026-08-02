<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\PortfolioAsset;
use App\Services\Market\PriceService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request, PriceService $prices)
    {
        $portfolios = auth()->user()->portfolios()->with('assets')->get();

        if ($request->expectsJson()) {
            return response()->json($portfolios);
        }

        $portfolioValues = [];
        foreach ($portfolios as $portfolio) {
            $total = 0.0;
            foreach ($portfolio->assets as $asset) {
                if ((float) $asset->quantity <= 0) {
                    continue;
                }
                try {
                    $total += (float) $asset->quantity * $prices->getCurrentPrice($asset->symbol);
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

        $portfolio = auth()->user()->portfolios()->create($data);

        return response()->json($portfolio, 201);
    }

    public function show(Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        return response()->json($portfolio->load('assets', 'trades'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_currency' => ['sometimes', 'string', 'max:10'],
        ]);

        $portfolio->update($data);

        return response()->json($portfolio);
    }

    public function destroy(Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $portfolio->delete();

        return response()->json(['message' => 'Portfolio deleted.']);
    }

    public function assets(Portfolio $portfolio, PriceService $prices)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        $portfolio->load('assets');

        foreach ($portfolio->assets as $asset) {
            try {
                $asset->current_price = $prices->getCurrentPrice($asset->symbol);
            } catch (\Throwable $e) {
                $asset->current_price = null;
            }
        }

        return response()->json($portfolio->assets);
    }

    public function performance(Portfolio $portfolio)
    {
        abort_unless($portfolio->user_id === auth()->id(), 403);

        return response()->json($portfolio->dailyPerformance()->orderBy('date')->get());
    }
}
