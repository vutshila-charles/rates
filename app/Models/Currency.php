<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http;
class Currency extends Model
{
    use HasFactory;
    public static function getRates(): array
    {
        return Cache::remember('forex_rates', 60, function () {
            $response = Http::get('https://www.completeapi.com/free_currencies.min.json');

            if ($response->successful()) {
                
                return $response->json()['forex'];
            }
            return [];
        });
        
    }
    public static function convert(float $amount, string $currencyCode): ?float
    {
        $rates = self::getRates();

        if (isset($rates[$currencyCode])) {
            return $amount * $rates[$currencyCode];
        }

        return null;
    }
}
