<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class UserWalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalValue = Transaction::where('user_wallet_id', $this->id)->sum('value');
        $toSQL = Transaction::where('user_wallet_id', $this->id)->toSql();
        Log::info('Data Wallet ' . $toSQL);
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'name' => $this->name,
            'totalValue' => $totalValue,
        ];
    }
}
