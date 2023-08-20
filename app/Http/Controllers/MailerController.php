<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Models\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MailerController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'from_name' => 'required|max:128',
                'to' => 'required|email',
                'subject' => 'required|max:255',
                'body' => 'required',
            ]);

            $fromName = $request->input('from_name');
            $emailTo = $request->input('to');
            $subject = $request->input('subject');
            $body = $request->input('body');

            $mail = new Mail;
            $mail->from_name = $fromName;
            $mail->email_to = $emailTo;
            $mail->subject = $subject;
            $mail->body = $body;
            $mail->save();

            SendEmail::dispatch($mail);

        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'status' => false,
            ]);
        }

        return response()->json([
            'error' => '',
            'status' => true,
        ]);
    }
}
