<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services', [
            'services' => Service::query()->orderBy('service_name')->get(),
            'addons' => Addon::query()->orderBy('addon_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:100'],
            'duration' => ['required', 'integer', 'min:1'],
            'happy_hour_price' => ['nullable', 'numeric', 'min:0', 'required_without:regular_price'],
            'regular_price' => ['nullable', 'numeric', 'min:0', 'required_without:happy_hour_price'],
        ]);

        Service::create($validated);

        return redirect()->route('services')->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:100'],
            'duration' => ['required', 'integer', 'min:1'],
            'happy_hour_price' => ['nullable', 'numeric', 'min:0', 'required_without:regular_price'],
            'regular_price' => ['nullable', 'numeric', 'min:0', 'required_without:happy_hour_price'],
        ]);

        $service->update($validated);

        return redirect()->route('services')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->appointments()->exists() || $service->transactionDetails()->exists()) {
            return redirect()->route('services')
                ->with('error', 'This service is already used in records and cannot be deleted.');
        }

        $service->delete();

        return redirect()->route('services')->with('success', 'Service deleted successfully.');
    }
}
