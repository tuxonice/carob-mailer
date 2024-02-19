<?php

namespace App\Http\Controllers;

use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SenderController extends Controller
{
    public function list(Request $request): View
    {
        $senderCollection = Sender::all();

        return view('sender.list', [
            'senderCollection' => $senderCollection,
        ]);
    }

}
