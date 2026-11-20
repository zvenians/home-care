<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = ['name','slug','category','description','icon','price','unit','min_units','duration_minutes','active'];
    protected function casts(): array { return ['active'=>'boolean','price'=>'integer','min_units'=>'integer','duration_minutes'=>'integer']; }
}
