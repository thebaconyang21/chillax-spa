<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PosCalculator
{
    /** @return array{service_price: float, addons: Collection<int, array{addon: Addon, price: float}>, subtotal: float, commission: float} */
    public function calculate(Service $service, array $addonIds, bool $appointmentLine, string $time, ?float $reservedPrice = null): array
    {
        if ($appointmentLine) {
            $servicePrice = (float) $reservedPrice;
        } else {
            $servicePrice = app(AppointmentScheduler::class)->priceFor($service, $time);
            if ($servicePrice === null) {
                throw ValidationException::withMessages(['lines' => "{$service->service_name} is not offered at this time."]);
            }
        }

        $addons = Addon::query()->whereIn('id', $addonIds)->get()->keyBy('id');
        if ($addons->count() !== count(array_unique($addonIds))) {
            throw ValidationException::withMessages(['lines' => 'One or more selected add-ons are invalid.']);
        }

        $isCombo = $service->service_name === 'Combo (Swedish Massage + 1 Add-on)';
        if ($isCombo && $addons->isEmpty()) {
            throw ValidationException::withMessages(['lines' => 'Select at least one add-on for the combo service.']);
        }

        $pricedAddons = collect();
        $addonTotal = 0.0;
        foreach (array_values(array_unique($addonIds)) as $index => $id) {
            $addon = $addons->get($id);
            $price = $isCombo && $index === 0 ? 0.0 : (float) $addon->price;
            $pricedAddons->push(['addon' => $addon, 'price' => round($price, 2)]);
            $addonTotal += $price;
        }

        $subtotal = round($servicePrice + $addonTotal, 2);

        return [
            'service_price' => round($servicePrice, 2),
            'addons' => $pricedAddons,
            'subtotal' => $subtotal,
            'commission' => round($subtotal * (float) config('spa.therapist_share'), 2),
        ];
    }
}
