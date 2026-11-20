<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceArea extends Model
{
    protected $fillable = ['name','city','coverage_km','active'];
    protected function casts(): array { return ['active'=>'boolean','coverage_km'=>'integer']; }
}
