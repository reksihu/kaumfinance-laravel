<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\TransactionType;
use App\Http\Requests\v1\StoreTransactionTypeRequest;
use App\Http\Requests\v1\UpdateTransactionTypeRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\TransactionTypeResource;
use App\Http\Resources\v1\TransactionTypeCollection;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new TransactionTypeCollection(TransactionType::paginate());
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
    public function store(StoreTransactionTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionType $transactionType)
    {
        return new TransactionTypeResource($transactionType);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionType $transactionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionTypeRequest $request, TransactionType $transactionType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        //
    }
}
