<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MailerController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        try {

            $validator = Validator::make($request->all(), [
                'from.name' => 'required|max:128',
                'to.name' => 'required|max:255',
                'to.email' => 'required|email',
                'subject' => 'required|max:255',
                'body.text' => 'present',
                'body.html' => 'required'
            ]);

            $validator->validate();

            $from = $request->input('from');
            $to = $request->input('to');
            $subject = $request->input('subject');
            $body = $request->input('body');

            $mail = new Mail;
            $mail->from_name = $from['name'];
            $mail->to_email = $to['email'];
            $mail->to_name = $to['name'];
            $mail->subject = $subject;
            $mail->body_text = $body['text'] ?? '';
            $mail->body_html = $body['html'];
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
