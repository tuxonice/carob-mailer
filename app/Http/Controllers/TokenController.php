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

    public function create(Request $request)
    {
        $request->validate([
            'token-label' => 'required',
        ]);

        $tokenLabel = $request->input('token-label');
        $token = $request->user()->createToken($tokenLabel);

        $message = 'Your token is: '. $token->plainTextToken;

        return redirect()->route('token.list')->with('info', $message);
    }
}
