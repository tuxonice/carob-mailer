<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\Mail;
use App\Rules\Base64File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
                'body.html' => 'required',
                'attachments' => 'array',
                'attachments.*.base64Content' => ['required', 'string', new Base64File],
                'attachments.*.originalFileName' => 'required|string',
            ]);

            $validator->validate();

            $from = $request->input('from');
            $to = $request->input('to');
            $subject = $request->input('subject');
            $body = $request->input('body');
            $attachements = [];
            if ($request->has('attachments')) {
                foreach ($request->input('attachments') as $item) {
                    $fileContent = base64_decode($item['base64Content']);
                    $attachFileName = Str::uuid()->toString();
                    Storage::disk('attachments')->put($attachFileName, $fileContent);
                    $attachements[] = [
                        'attachFileName' => $attachFileName,
                        'originalFileName' => $item['originalFileName'],
                    ];
                }
            }

            $mail = new Mail;
            $mail->setFromName($from['name']);
            $mail->setToEmail($to['email']);
            $mail->setToName($to['name']);
            $mail->setSubject($subject);
            $mail->setBodyText($body['text'] ?? '');
            $mail->setBodyHtml($body['html']);
            $mail->setAttachments(json_encode($attachements));
            $mail->save();

            SendEmail::dispatch($mail);

        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'status' => false,
            ], 422);
        }

        return response()->json([
            'error' => '',
            'status' => true,
        ]);
    }
}
