<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('auth.landing-page');  // Pastikan ada file view landing.blade.php
    }
}
