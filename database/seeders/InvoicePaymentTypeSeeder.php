<?php

namespace Database\Seeders;

use App\Models\InvoicePaymentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoicePaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            InvoicePaymentType::PREPAYMENT_NAME,
            InvoicePaymentType::FINAL_PAYMENT_NAME,
            InvoicePaymentType::FULL_PAYMENT_NAME,
        ];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new InvoicePaymentType();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}
