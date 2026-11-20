<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = ['name','email','phone','profession','experience_years','motivation','status'];
    protected function casts(): array { return ['experience_years'=>'integer']; }
}
