<?php
namespace App\Http\Controllers\Api;

use App\Models\{Booking,Caregiver,Customer,Promotion,Service};
use App\Services\PricingEngine;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function store(Request $request, PricingEngine $engine)
    {
        abort_unless(config('homecare.demo_mode'),403);
        $input=$request->validate(array_merge(QuoteController::rules(),[
            'name'=>['required','string','min:2','max:120'],
            'phone'=>['required','string','regex:/^\+?[0-9\s-]{9,20}$/'],
            'email'=>['nullable','email','max:180'],
            'address'=>['required','string','min:8','max:500'],
            'patient_note'=>['nullable','string','max:1000'],
        ]));
        $date = CarbonImmutable::parse($input['scheduled_date'].' '.$input['scheduled_time'],'Asia/Jakarta');
        if ($date->lessThanOrEqualTo(now('Asia/Jakarta'))) return response()->json(['message'=>'Pilih jadwal di masa mendatang.'],422);
        $service=Service::where('active',true)->findOrFail($input['service_id']);
        if ($input['units'] < $service->min_units) return response()->json(['message'=>'Durasi kurang dari minimum layanan.'],422);
        $promo = !empty($input['promo_code']) ? Promotion::where('code',strtoupper(trim($input['promo_code'])))->first() : null;
        if (!empty($input['promo_code']) && (!$promo || !$promo->active || ($promo->expires_at && $promo->expires_at->endOfDay()->isPast()))) return response()->json(['message'=>'Kode promo tidak berlaku.'],422);
        $quote=$engine->calculate($service,$input,$promo);
        $booking = DB::transaction(function () use ($request,$input,$quote) {
            $phone=preg_replace('/[^0-9+]/','',$input['phone']);
            $customer=Customer::firstOrCreate(['phone'=>$phone],['name'=>$input['name'],'email'=>$input['email']??null,'address'=>$input['address']]);
            $customer->update(['name'=>$input['name'],'email'=>$input['email']??null,'address'=>$input['address']]);
            $booking=Booking::create([
                'code'=>'HC-'.now()->format('ymd').'-'.strtoupper(Str::random(5)),
                'service_id'=>$input['service_id'],'customer_id'=>$customer->id,
                'scheduled_date'=>$input['scheduled_date'],'scheduled_time'=>$input['scheduled_time'],
                'units'=>$input['units'],'distance_km'=>$input['distance_km'],'urgent'=>$input['urgent']??false,
                'addons'=>$input['addons']??[],'promo_code'=>$input['promo_code']??null,
                'patient_note'=>$input['patient_note']??null,'address'=>$input['address'],
                'price_breakdown'=>$quote,'total'=>$quote['total'],'status'=>'pending',
            ]);
            $request->session()->put('demo_customer_id',$customer->id);
            return $booking;
        });
        return response()->json(['message'=>'Pesanan demo berhasil dibuat.','booking'=>$booking->load(['service','customer','caregiver'])],201);
    }

    public function update(Request $request, Booking $booking)
    {
        $input=$request->validate([
            'status'=>['sometimes',Rule::in(['pending','confirmed','assigned','on_the_way','in_progress','completed','cancelled'])],
            'caregiver_id'=>['sometimes','nullable','integer','exists:caregivers,id'],
        ]);
        if (array_key_exists('caregiver_id',$input) && $input['caregiver_id']) {
            $staff=Caregiver::findOrFail($input['caregiver_id']);
            if (!$staff->available) return response()->json(['message'=>'Tenaga kesehatan sedang tidak tersedia.'],422);
            $start=CarbonImmutable::parse($booking->scheduled_date->format('Y-m-d').' '.$booking->scheduled_time);
            $duration = $booking->service->duration_minutes * $booking->units;
            $end=$start->addMinutes($duration);
            $busy=Booking::where('caregiver_id',$staff->id)->where('scheduled_date',$booking->scheduled_date->format('Y-m-d'))
                ->whereNotIn('status',['cancelled'])->where('id','!=',$booking->id)
                ->with('service')->get()->contains(function ($other) use ($start,$end) {
                    $otherStart=CarbonImmutable::parse($other->scheduled_date->format('Y-m-d').' '.$other->scheduled_time);
                    $otherEnd=$otherStart->addMinutes($other->service->duration_minutes * $other->units);
                    return $start->lessThan($otherEnd) && $otherStart->lessThan($end);
                });
            if ($busy) return response()->json(['message'=>'Jadwal tenaga kesehatan bentrok dengan pesanan lain.'],422);
        }
        $booking->update($input);
        return response()->json(['booking'=>$booking->fresh()->load(['service','customer','caregiver'])]);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(['message'=>'Pesanan dihapus.']);
    }
}
