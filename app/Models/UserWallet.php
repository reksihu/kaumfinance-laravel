<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
    ];

    public function transaction() {
        return $this->hasMany(Transaction::class);
    }

    public function user() {
        return $this->belongsTo(user::class);
    }
}
