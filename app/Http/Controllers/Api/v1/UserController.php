<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use App\Http\Requests\v1\StoreUserRequest;
use App\Http\Requests\v1\UpdateUserRequest;
use App\Http\Requests\v1\DeleteUserRequest;
use App\Http\Requests\v1\SelectUserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserResource;
use App\Http\Resources\v1\UserCollection;
use App\Filters\v1\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $filter = new UserFilter();
        $queryItems = $filter->transform($request);

        $user = User::where('id', $userId)->where($queryItems)->get();
        return new UserCollection($user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    // This is for registration
    public function store(StoreUserRequest $request)
    {
        $request->merge([
            'password' => Hash::make($request['password']),
            'remember_token' => Str::random(10),
            // Next will make it as account verification action
            'email_verified_at' => now(),
        ]);
        $user = User::create($request->all());
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(SelectUserRequest $request, User $user)
    {
        return new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if ($request['password']) {
            $request['password'] = Hash::make($request['password']);
        }
        $user->update($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
    }
}
