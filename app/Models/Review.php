<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['name','service_id','rating','comment','published'];
    protected function casts(): array { return ['published'=>'boolean','rating'=>'integer']; }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}
