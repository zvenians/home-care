<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = ['code','type','value','max_discount','active','expires_at'];
    protected function casts(): array { return ['active'=>'boolean','expires_at'=>'date:Y-m-d','value'=>'integer','max_discount'=>'integer']; }
}
