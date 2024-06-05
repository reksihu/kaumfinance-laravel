<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'date',
        'transaction_type_id',
        'user_wallet_id',
        'value',
        'category',
        'sub_category',
    ];

    /* public function user() {
        return $this->belongsTo(User::class);
    } */

    public function transactionType() {
        return $this->belongsTo(TransactionType::class);
    }

    public function userWallet() {
        return $this->belongsTo(UserWallet::class);
    }
}
