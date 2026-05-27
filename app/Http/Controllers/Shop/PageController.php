<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function about()
    {
        return view('shop.pages.about');
    }

    public function contact()
    {
        return view('shop.pages.contact');
    }

    public function shippingReturns()
    {
        return view('shop.pages.shipping-returns');
    }

    public function privacy()
    {
        return view('shop.pages.privacy');
    }

    public function terms()
    {
        return view('shop.pages.terms');
    }
}
