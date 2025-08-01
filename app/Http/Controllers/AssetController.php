<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::orderBy('date', 'desc')->get();
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'value' => 'required|numeric|min:0',
            'date'  => 'required|date',
        ]);

        Asset::create($request->all());

        return redirect()->route('assets-inventory.index')->with('success', 'Asset created successfully.');
    }

    public function edit(Asset $assets_inventory)
    {
        return view('assets.edit', ['asset' => $assets_inventory]);
    }

    public function update(Request $request, Asset $assets_inventory)
    {
        $request->validate([
            'title' => 'required',
            'value' => 'required|numeric|min:0',
            'date'  => 'required|date',
        ]);

        $assets_inventory->update($request->all());

        return redirect()->route('assets-inventory.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $assets_inventory)
    {
        $assets_inventory->delete();

        return redirect()->route('assets-inventory.index')->with('success', 'Asset deleted successfully.');
    }
}
