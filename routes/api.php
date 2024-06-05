<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

Route::post('/generate-token', function (Request $request) {
    $credentials = [
        'email' => $request->email,
        'password' => $request->password
    ];
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        // return $user->id;
        // dd($user);
        $issuedTokens = PersonalAccessToken::where('tokenable_type', 'App\Models\User')
        ->where('tokenable_id', $user->id)
        ->whereBetween('created_at', [
            Carbon::today(),
            Carbon::today()->endOfDay()
        ])
        ->get();
        
        if ($issuedTokens->count() > 10) {
            return response()->json(['message' => 'Max Limit Today.']);
        }

        $basicToken = $user -> createToken('basic-token', ['none'], expiresAt: now()->addMinutes(30));
        return [
            'basic' => $basicToken->plainTextToken
        ];
    } else {
        return response()->json(['message' => 'Unauthenticated.']);
    }
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\v1', 'middleware' => 'auth:sanctum'], function() {
    Route::apiResource('transaction', TransactionController::class);
    Route::apiResource('transaction-type', TransactionTypeController::class);
});