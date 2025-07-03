<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IpCheckController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/check-ip', [IpCheckController::class, 'check'])->middleware('restrict.ip');


Route::middleware('restrict.ip')->group(function () {
    Route::get('/test', function (Request $request) {
        return response()->json([
            'message' => 'Access granted!',
            'ip' => $request->ip(),
            'test_ip' => $request->query('test_ip', $request->header('X-Test-IP')) ?: 'not set',
        ]);
    });
});
