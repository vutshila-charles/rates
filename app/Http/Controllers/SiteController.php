<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
class SiteController extends Controller
{
    public function viewHome()
    {
        return view('home');
    }

    public function fetchRates()
    {
        return response()->json(Currency::getRates());
        
    }

    public function convertCurrency(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|min:3',
        ]);
    
        $amount = $validatedData['amount'];
        $currency = $validatedData['currency'];
        $convertedAmount = Currency::convert($amount, $currency);
    
        return response()->json(['convertedAmount' => $convertedAmount]);
    }

}
