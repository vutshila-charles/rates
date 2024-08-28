<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyTest extends TestCase
{
    public function testGetRatesCachesAndReturnsRates()
    {
        Http::fake([
            'https://www.completeapi.com/free_currencies.min.json' => Http::response([
                'forex' => [
                    'USD' => 1.0,
                    'EUR' => 0.9,
                    'JPY' => 110.0,
                ],
            ]),
        ]);
        Cache::shouldReceive('remember')
            ->once()
            ->with('forex_rates', 60, \Closure::class)
            ->andReturn([
                'USD' => 1.0,
                'EUR' => 0.9,
                'JPY' => 110.0,
            ]);

        $rates = Currency::getRates();

        $this->assertIsArray($rates);
        $this->assertArrayHasKey('USD', $rates);
        $this->assertArrayHasKey('EUR', $rates);
        $this->assertArrayHasKey('JPY', $rates);
        $this->assertEquals(1.0, $rates['USD']);
        $this->assertEquals(0.9, $rates['EUR']);
        $this->assertEquals(110.0, $rates['JPY']);
    }

    public function testConvertReturnsCorrectAmount()
    {
        Http::fake([
            'https://www.completeapi.com/free_currencies.min.json' => Http::response([
                'forex' => [
                    'USD' => 1.0,
                    'EUR' => 0.9,
                    'JPY' => 110.0,
                ],
            ]),
        ]);
        Cache::shouldReceive('remember')
            ->once()
            ->with('forex_rates', 60, \Closure::class)
            ->andReturn([
                'USD' => 1.0,
                'EUR' => 0.9,
                'JPY' => 110.0,
            ]);

        $convertedAmount = Currency::convert(100, 'EUR');

        $this->assertEquals(90.0, $convertedAmount);
    }

    public function testConvertReturnsNullForInvalidCurrency()
    {
        Http::fake([
            'https://www.completeapi.com/free_currencies.min.json' => Http::response([
                'forex' => [
                    'USD' => 1.0,
                    'EUR' => 0.9,
                    'JPY' => 110.0,
                ],
            ]),
        ]);
        Cache::shouldReceive('remember')
            ->once()
            ->with('forex_rates', 60, \Closure::class)
            ->andReturn([
                'USD' => 1.0,
                'EUR' => 0.9,
                'JPY' => 110.0,
            ]);

        $convertedAmount = Currency::convert(100, 'GBP');

        $this->assertNull($convertedAmount);
    }
}
