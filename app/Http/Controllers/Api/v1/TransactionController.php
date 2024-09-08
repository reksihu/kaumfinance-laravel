<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Models\Transaction;
use App\Http\Requests\v1\StoreTransactionRequest;
use App\Http\Requests\v1\UpdateTransactionRequest;
use App\Http\Requests\v1\DeleteTransactionRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\TransactionResource;
use App\Http\Resources\v1\TransactionCollection;
use App\Filters\v1\TransactionsFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // return Transaction::all();
        // Get data user from request token
        $user = $request->user();
        $userId = $request->user()->id;
        $userReportDatePeriod = $request->user()->report_date_period;
        if ($userReportDatePeriod == null || $userReportDatePeriod <= 1) {
            $userReportDateStart = Carbon::now()->startOfMonth();
            $userReportDateEnd = $userReportDateStart->clone()->endOfMonth();
        } else {
            $userReportDateStart = Carbon::now()->startOfMonth()->day($userReportDatePeriod);
            // Check is today less then date report periode that set in the database
            if (Carbon::today()->format('j') <=  $userReportDatePeriod) {
                // If yes then set the start date with the previous month
                $userReportDateStart = $userReportDateStart->clone()->subMonth();
            }
            $userReportDateEnd = $userReportDateStart->clone()->addMonth()->subDay(1)->endOfDay();
        }
        Log::info('User ' . $userId . ' Report Dates: Start: ' . $userReportDateStart . ', End: ' . $userReportDateEnd);
        $filter = new TransactionsFilter();
        $queryItems = $filter->transform($request); // ['column', 'operator', 'value']
        if (count($queryItems) == 0) {
            $transactions = Transaction::with('userWallet.user')->whereHas('userWallet.user', function ($query) use ($userId) {
                $query->where('id', $userId);
            })->whereBetween('date', [$userReportDateStart, $userReportDateEnd])->paginate();
            return new TransactionCollection($transactions);
        } else {
            // $transactions = Transaction::where($queryItems)->paginate();
            $transactions = Transaction::with('userWallet.user')->whereHas('userWallet.user', function ($query) use ($userId) {
                $query->where('id', $userId);
            })->whereBetween('date', [$userReportDateStart, $userReportDateEnd])->where($queryItems)->paginate();
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
    public function destroy(DeleteTransactionRequest $request, Transaction $transaction)
    {
        $transaction->delete();
    }
}
