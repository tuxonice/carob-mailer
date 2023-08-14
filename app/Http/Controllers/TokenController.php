<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TokenController extends Controller
{
    public function list(Request $request): View
    {
        $tokenList = $request->user()->tokens;

        return view('token.list', [
            'tokenList' => $tokenList,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'token-label' => 'required',
        ]);

        $tokenLabel = $request->input('token-label');
        $token = $request->user()->createToken($tokenLabel);

        $message = 'Your token is: '.$token->plainTextToken;

        return redirect()->route('token.list')->with('info', $message);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'token-id' => 'required|integer',
        ]);

        $tokenId = $request->input('token-id');
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('token.list');
    }
}
