<?php

namespace App\Http\Controllers;

use App\Traits\MidtransPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    use MidtransPayment;

    /**
     * Show the checkout success view
     */
    public function success(): View
    {
        return view('checkout.checkout-success');
    }

    /**
     * Show the checkout pending view
     */
    public function pending(): View
    {
        return view('checkout.checkout-pending');
    }
    /**
     * Show the checkout failed view
     */
    public function failed(): View
    {
        return view('checkout.checkout-failed');
    }
}
