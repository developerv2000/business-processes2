<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    const EXCHANGE_RATE_API_URL = 'https://v6.exchangerate-api.com/v6/2b3965359716e1bb35e7a237/latest/';

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /**
     * Update all currencies except USD using an external API.
     *
     * This method is used for updating currencies via a cron job every day.
     *
     * @return void
     */
    public static function updateExchangeRatesExceptUSD()
    {
        self::where('name', '!=', 'USD')->each(function ($instance) {
            $response = Http::get(self::EXCHANGE_RATE_API_URL . $instance->name);
            $instance->usd_ratio = ($response->json())['conversion_rates']['USD'];
            $instance->save();
        });
    }

    public static function getAll()
    {
        return self::orderBy('id')->get();
    }

    /**
     * Convert the given price from the specified currency to USD.
     *
     * @param float $price The price to convert.
     * @param string $currencyName The name of the currency to convert from.
     * @return float The converted price in USD.
     */
    public static function convertPriceToUSD(float $price, string $currencyName): float
    {
        // Retrieve the currency information from the database
        $currency = self::where('name', $currencyName)->first();

        // If the currency is found, calculate the converted price
        if ($currency) {
            $converted = $price * $currency->usd_ratio;
            return $converted;
        } else {
            // If the currency is not found, return the original price
            return $price;
        }
    }

    public static function getDefaultCurrencyForOrder()
    {
        return self::where('name', 'EUR')->first();
    }
}
