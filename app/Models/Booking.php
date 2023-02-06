<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'field_id',
        'match_id',
        'type_id',
        'when',
        'message',
        'status',
        'have_notifications',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function field() {
        return $this->belongsTo(Field::class);
    }

    public function match() {
        return $this->belongsTo(Match::class);
    }

    public function type() {
        return $this->belongsTo(Type::class);
    }
}
