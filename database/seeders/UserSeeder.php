<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\TransactionType;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        /* User::factory()->count(20)
        ->has(UserWallet::factory(count:5)
            ->has(Transaction::factory(count:5))
        )
        ->create(); */

        $transactionTypes = TransactionType::all();

        // Create Users with UserWallets and Transactions
        User::factory()->count(20)->create()->each(function (User $user) use ($faker, $transactionTypes) {
            $userWallets = [];

            // Generate UserWallets for each User (adjust count as needed)
            for ($i = 0; $i < rand(1, 3); $i++) {
                $userWallets[] = UserWallet::factory()->create([
                    'user_id' => $user->id,
                ]);
            }

            // Create Transactions within each UserWallet
            foreach ($userWallets as $userWallet) {
                $transactions = [];
                for ($j = 0; $j < rand(2, 5); $j++) {
                    $transactions[] = [
                        'date' => $faker->dateTimeThisDecade(),
                        'transaction_type_id' => $transactionTypes->random()->id,
                        'user_wallet_id' => $userWallet->id, // Generate for this user user_wallet_id
                        'value' => $faker->numberBetween(-100000, 1000000),
                        'category' => $faker->word,
                        'sub_category' => $faker->word,
                    ];
                }

                // Create Transactions associated with the UserWallet
                Transaction::insert($transactions);
            }
        });
    }
}
