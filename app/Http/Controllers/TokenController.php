<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TokenController extends Controller
{
    public function list(Request $request): View
    {
        $tokenList = $request->user()->tokens;



        return view('token.list', [
            'tokenList' => $tokenList,
        ]);
    }

    public function create(Request $request): View
    {
        $tokenList = $request->user()->tokens;

        $title = $request->input('token-title');

        return view('token.list', [
            'tokenList' => $tokenList,
        ]);
    }
}
