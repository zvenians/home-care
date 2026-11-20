<?php
namespace App\Services;

use App\Models\Promotion;
use App\Models\Service;
use Carbon\CarbonImmutable;

/** All monetary amounts are Indonesian rupiah (integer), with server as source of truth. */
class PricingEngine
{
    public function calculate(Service $service, array $input, ?Promotion $promo = null): array
    {
        $units = max($service->min_units, (int) ($input['units'] ?? $service->min_units));
        $distance = (float) ($input['distance_km'] ?? 0);
        $datetime = CarbonImmutable::parse(($input['scheduled_date'] ?? now()->toDateString()).' '.($input['scheduled_time'] ?? '09:00'), 'Asia/Jakarta');
        $base = (int) $service->price * $units;
        $travel = (int) (ceil(max(0, $distance - config('homecare.free_radius_km', 5))) * config('homecare.additional_km_fee', 4500));
        $night = ($datetime->hour >= 20 || $datetime->hour < 6) ? (int) round($base * config('homecare.night_surcharge_pct', 20) / 100) : 0;
        $weekend = $datetime->isWeekend() ? (int) round($base * config('homecare.weekend_surcharge_pct', 10) / 100) : 0;
        $urgent = !empty($input['urgent']) ? (int) config('homecare.urgent_fee', 75000) : 0;
        $addons = [];
        $addonTotal = 0;
        foreach (($input['addons'] ?? []) as $code) {
            $data = config("homecare.addons.$code");
            if (!$data) continue;
            $addons[] = ['code' => $code, 'name' => $data['name'], 'amount' => $data['price']];
            $addonTotal += $data['price'];
        }
        $discount = 0;
        if ($promo && $promo->active && (!$promo->expires_at || $promo->expires_at->endOfDay()->greaterThanOrEqualTo(now()))) {
            $discount = $promo->type === 'percent' ? (int) round($base * $promo->value / 100) : (int) $promo->value;
            if ($promo->max_discount > 0) $discount = min($discount, (int) $promo->max_discount);
            $discount = min($discount, $base);
        }
        $platform = (int) config('homecare.platform_fee', 5000);
        return [
            'service_name'=>$service->name, 'unit'=>$service->unit, 'units'=>$units,
            'base'=>$base,'travel'=>$travel,'night'=>$night,'weekend'=>$weekend,
            'urgent'=>$urgent,'addons'=>$addons,'addon_total'=>$addonTotal,
            'discount'=>$discount,'platform'=>$platform,
            'total'=>max(0,$base+$travel+$night+$weekend+$urgent+$addonTotal+$platform-$discount),
            'distance_km'=>$distance, 'promo_code'=>$discount > 0 ? $promo?->code : null,
            'pricing_note'=>'Estimasi berdasar jarak input manual. Jadwal & ketersediaan dikonfirmasi tim. Belum ada pembayaran online.',
        ];
    }
}
