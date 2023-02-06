<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_key',
        'sport_id',
    ];

    public function sport() {
        return $this->belongsTo(Sport::class);
    }

    public function fields() {
        return $this->belongsToMany(Field::class);
    }
}
