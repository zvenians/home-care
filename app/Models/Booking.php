<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = ['code','service_id','customer_id','caregiver_id','scheduled_date','scheduled_time','units','distance_km','urgent','addons','promo_code','patient_note','address','status','price_breakdown','total'];
    protected function casts(): array { return ['scheduled_date'=>'date:Y-m-d','distance_km'=>'float','urgent'=>'boolean','addons'=>'array','price_breakdown'=>'array','total'=>'integer']; }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function caregiver(): BelongsTo { return $this->belongsTo(Caregiver::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
