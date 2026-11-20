<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caregiver extends Model
{
    protected $fillable = ['name','role','speciality','experience_years','city','rating','available','initials','accent','bio'];
    protected function casts(): array { return ['available'=>'boolean','rating'=>'float','experience_years'=>'integer']; }
}
