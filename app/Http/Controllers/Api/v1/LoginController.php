<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request) {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::attempt($credentials)) {
            Log::info('User ' . $request->password);
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
                return response()->json(['message' => 'Max Limit Today.'], 429);
            }

            $basicToken = $user -> createToken('basic-token', ['create', 'update', 'delete'], expiresAt: now()->addMonths(3));
            return response()->json(['message' => $basicToken->plainTextToken], 200);
        } else {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }
}
