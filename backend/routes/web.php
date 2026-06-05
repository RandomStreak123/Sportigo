<?php

use App\Http\Controllers\OtpController;

Route::get('/', function () {
    return response()->json(['message' => 'PlayConnect API is running successfully.']);
});

Route::post('/otp/send', [OtpController::class, 'send'])->name('otp.send');
Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');

Route::get('/{name}', function ($name=null) {
    $data=compact('name');
    return view('home')->with($data);
});
