<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetLabelController extends Controller
{
    /**
     * Print labels for single or multiple assets
     */
    public function print(Request $request)
    {
        $assetIds = $request->input('assets', []);

        if (empty($assetIds)) {
            return redirect()->back()->with('error', 'No assets selected for printing');
        }

        // Load assets with relationships
        $assets = Asset::with(['category', 'location'])
            ->whereIn('id', $assetIds)
            ->get();

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'Assets not found');
        }

        return view('filament.pages.print-asset-labels', [
            'assets' => $assets,
        ]);
    }

    /**
     * Print label for a single asset
     */
    public function printSingle($id)
    {
        $asset = Asset::with(['category', 'location'])->findOrFail($id);

        return view('filament.pages.print-asset-labels', [
            'assets' => collect([$asset]),
        ]);
    }
}
