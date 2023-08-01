<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class Dashboard extends Controller
{
    public function index(): View
    {
        return view('dashboard', [

        ]);
    }
}
