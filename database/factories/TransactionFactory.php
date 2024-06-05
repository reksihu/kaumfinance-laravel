<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\TransactionType;
use App\Models\UserWallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeThisDecade(),
            'transaction_type_id' => TransactionType::factory(),
            'user_wallet_id' => UserWallet::factory(),
            'value' => $this->faker->numberBetween(-100000, 1000000),
            'category' => $this->faker->word(),
            'sub_category' => $this->faker->word()
        ];
    }
}
