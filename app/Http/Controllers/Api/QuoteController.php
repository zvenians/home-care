<?php
namespace App\Http\Controllers\Api;

use App\Models\Promotion;
use App\Models\Service;
use App\Services\PricingEngine;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class QuoteController extends Controller
{
    public static function rules(): array
    {
        return [
            'service_id'=>['required','integer','exists:services,id'],
            'units'=>['required','integer','min:1','max:30'],
            'distance_km'=>['required','numeric','min:0','max:200'],
            'scheduled_date'=>['required','date_format:Y-m-d'],
            'scheduled_time'=>['required','date_format:H:i'],
            'urgent'=>['sometimes','boolean'],
            'addons'=>['sometimes','array','max:3'],
            'addons.*'=>['string',Rule::in(array_keys(config('homecare.addons'))),'distinct'],
            'promo_code'=>['nullable','string','max:30'],
        ];
    }

    public function quote(Request $request, PricingEngine $engine)
    {
        $input = $request->validate(self::rules());
        $service = Service::where('active',true)->findOrFail($input['service_id']);
        abort_if($input['units'] < $service->min_units, 422, "Minimal {$service->min_units} {$service->unit}.");
        $promo = null;
        if (!empty($input['promo_code'])) {
            $promo = Promotion::where('code',strtoupper(trim($input['promo_code'])))->first();
            if (!$promo || !$promo->active || ($promo->expires_at && $promo->expires_at->endOfDay()->isPast())) {
                return response()->json(['message'=>'Kode promo tidak berlaku.'],422);
            }
        }
        return response()->json(['quote'=>$engine->calculate($service,$input,$promo)]);
    }
}
