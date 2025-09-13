<?php

use App\Http\Controllers\Api\MailerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Add throttle middleware to limit requests to 60 per minute
Route::middleware(['auth:sanctum', 'throttle:20,1'])->post('/mailer/send', [MailerController::class, 'send']);
