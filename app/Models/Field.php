<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'currency_id',
        'name',
        'address',
        'description',
        'cost',
        'image',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    public function types() {
        return $this->belongsToMany(Type::class)->withPivot('cost');
    }

}
