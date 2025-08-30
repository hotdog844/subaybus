<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function faq()
    {
        return view('pages.faq');
    }

    // Add this new method
    public function settings()
    {
        return view('mobile_settings');
    }
}