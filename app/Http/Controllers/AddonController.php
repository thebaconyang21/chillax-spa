<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'addon_name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        Addon::create($validated);

        return redirect()->route('services')->with('success', 'Add-on added successfully.');
    }

    public function update(Request $request, Addon $addon): RedirectResponse
    {
        $validated = $request->validate([
            'addon_name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $addon->update($validated);

        return redirect()->route('services')->with('success', 'Add-on updated successfully.');
    }

    public function destroy(Addon $addon): RedirectResponse
    {
        if ($addon->detailAddons()->exists()) {
            return redirect()->route('services')
                ->with('error', 'This add-on is already used in records and cannot be deleted.');
        }

        $addon->delete();

        return redirect()->route('services')->with('success', 'Add-on deleted successfully.');
    }
}
