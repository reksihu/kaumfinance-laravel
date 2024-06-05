<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Transaction;
use App\Http\Requests\v1\StoreTransactionRequest;
use App\Http\Requests\v1\UpdateTransactionRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\TransactionResource;
use App\Http\Resources\v1\TransactionCollection;
use App\Filters\v1\TransactionsFilter;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // return Transaction::all();
        $filter = new TransactionsFilter();
        $queryItems = $filter->transform($request); // ['column', 'operator', 'value']
        if (count($queryItems) == 0) {
            return new TransactionCollection(Transaction::paginate());
        } else {
            $transactions = Transaction::where($queryItems)->paginate();
            return new TransactionCollection($transactions->appends($request->query())); // appends is to add the current filtering
        }
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
    public function store(StoreTransactionRequest $request)
    {
        return new TransactionResource(Transaction::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        // return $transaction;
        return new TransactionResource($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
