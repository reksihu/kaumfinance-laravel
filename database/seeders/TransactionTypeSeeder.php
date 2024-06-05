<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Pemasukan'],
            ['name' => 'Pengeluaran'],
            ['name' => 'Transfer'],
            ['name' => 'Piutang'],
            ['name' => 'Hutang'],
        ];

        TransactionType::insert($data);
    }
}
