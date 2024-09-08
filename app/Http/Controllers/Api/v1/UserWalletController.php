<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\UserWallet;
use App\Http\Requests\v1\StoreUserWalletRequest;
use App\Http\Requests\v1\UpdateUserWalletRequest;
use App\Http\Requests\v1\DeleteUserWalletRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\UserWalletResource;
use App\Http\Resources\v1\UserWalletCollection;
use App\Filters\v1\UserWalletFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $filter = new UserWalletFilter();
        $queryItems = $filter->transform($request);

        $userWallet = UserWallet::where('user_id', $userId)->where($queryItems)->paginate();
        return new UserWalletCollection($userWallet);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserWalletRequest $request)
    {
        $userId = $request->user()->id;
        // Customize request data using the user's information
        $request->merge([
            'user_id' => $userId,
        ]);

        $userWallet = UserWallet::create($request->all());
        return new UserWalletResource($userWallet);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserWallet $userWallet)
    {
        return new UserWalletResource($userWallet);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserWallet $userWallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserWalletRequest $request, UserWallet $userWallet)
    {
        $userWallet->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserWalletRequest $request, UserWallet $userWallet)
    {
        $userWallet->delete();
    }
}
