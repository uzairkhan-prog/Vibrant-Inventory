<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Cash', 'Bank', 'JazzCash', 'EasyPaisa'];

        foreach ($types as $type) {
            PaymentType::firstOrCreate(['name' => $type]);
        }
    }
}

