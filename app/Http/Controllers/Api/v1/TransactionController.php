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
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // helper function to generate the report dates from request and database
    function getReportDates($request) {
        if ($request->startDate && $request->endDate) {
            $userReportDateStart = $request->startDate;
            $userReportDateEnd = $request->endDate;
        }
        else {
            $userReportDatePeriod = $request->user()->report_date_period;
            if ($userReportDatePeriod == null || $userReportDatePeriod <= 1) {
                $userReportDateStart = Carbon::now()->startOfMonth();
                $userReportDateEnd = $userReportDateStart->clone()->endOfMonth();
            } else {
                $userReportDateStart = Carbon::now()->startOfMonth()->day($userReportDatePeriod);
                // Check is today less then date report periode that set in the database
                if (Carbon::today()->format('j') <  $userReportDatePeriod) {
                    // If yes then set the start date with the previous month
                    $userReportDateStart = $userReportDateStart->clone()->subMonth();
                }
                $userReportDateEnd = $userReportDateStart->clone()->addMonth()->subDay(1)->endOfDay();
            }
        }
        Log::info('User ' . $request->user() . ' Report Dates: Start: ' . $userReportDateStart . ', End: ' . $userReportDateEnd);
        return ['userReportDateStart' => $userReportDateStart, 'userReportDateEnd' => $userReportDateEnd];
    }
    
    public function index(Request $request)
    {
        // return Transaction::all();
        // Get data user from request token
        $user = $request->user();
        $userId = $request->user()->id;
        
        $values = TransactionController::getReportDates($request);
        $userReportDateStart = $values['userReportDateStart'];
        $userReportDateEnd = $values['userReportDateEnd'];

        $filter = new TransactionsFilter();
        $queryItems = $filter->transform($request); // ['column', 'operator', 'value']
        Log::info('Filter ' . json_encode($queryItems));

        $transactions = Transaction::
        leftJoin('transaction_types AS tt', 'transactions.transaction_type_id', '=', 'tt.id')
        ->leftJoin('user_wallets AS uw', 'uw.id', '=', 'transactions.user_wallet_id')
        ->where('uw.user_id', $userId)
        ->whereBetween('transactions.date', [$userReportDateStart, $userReportDateEnd]);

        if (count($queryItems) == 0) {
            $transactions = $transactions->paginate();
            return new TransactionCollection($transactions);
        } else {
            $transactions = $transactions->where($queryItems);
            Log::info($transactions->toSql());
            $transactions = $transactions->paginate();
            // appends is to add the current filtering
            return new TransactionCollection($transactions->appends($request->query()));
        }
    }

    public function getInOut(Request $request)
    {
        $user = $request->user();
        $userId = $request->user()->id;
        
        $values = TransactionController::getReportDates($request);
        $userReportDateStart = $values['userReportDateStart'];
        $userReportDateEnd = $values['userReportDateEnd'];
        
        $transactions = Transaction::select('tt.name AS name', DB::raw('SUM(transactions.value) AS total_value'))
        ->leftJoin('transaction_types AS tt', 'transactions.transaction_type_id', '=', 'tt.id')
        ->leftJoin('user_wallets AS uw', 'uw.id', '=', 'transactions.user_wallet_id')
        ->where('uw.user_id', $userId)
        ->whereBetween('transactions.date', [$userReportDateStart, $userReportDateEnd])
        ->groupBy('tt.name');
        Log::info($transactions->toSql());
        $transactions = $transactions->get();
        return response()->json($transactions);
    }

    public function getCategory(Request $request)
    {
        $user = $request->user();
        $userId = $request->user()->id;
        
        $values = TransactionController::getReportDates($request);
        $userReportDateStart = $values['userReportDateStart'];
        $userReportDateEnd = $values['userReportDateEnd'];

        $transactions = Transaction::select('transactions.category AS category', DB::raw('SUM(transactions.value) AS total_value'))
        ->leftJoin('user_wallets AS uw', 'uw.id', '=', 'transactions.user_wallet_id')
        ->where('uw.user_id', $userId)
        ->whereBetween('transactions.date', [$userReportDateStart, $userReportDateEnd])
        ->groupBy('transactions.category');
        Log::info($transactions->toSql());
        $transactions = $transactions->get();
        return response()->json($transactions);
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
        // 1: Pemasukan
        if ($request['transaction_type_id'] == 1) {
            // Do nothing
        }
        // 2: Pengeluaran
        else if ($request['transaction_type_id'] == 2) {
            // Adjust value if not minus
            if ($request['value'] > 0) {
                $request['value'] = $request['value'] * -1;
            }
        }
        // 3: Penyesuaian Saldo
        else if ($request['transaction_type_id'] == 3) {
            // Cek total saldo per dompet
            // Value untuk di save = Nominal request - Total saldo per dompet
            // Misal saldo di rekening/seharusnya = 500,000
            // Total saldo di dompet = 600,000
            // Maka value untuk di save -100,000 (untuk penyesuaiannya)
            $totalValue = Transaction::where('user_wallet_id', $request['user_wallet_id'])->sum('value');
            $request['value'] = $request['value'] - $totalValue;
        }
        // 4 Transfer
        else if ($request['transaction_type_id'] == 4) {
            if ($request['value'] < 0) {
                return response()->json(['message' => 'Kamu tidak boleh mengisi nominal minus.'], 400);
            }
            if (!$request['user_wallet_id_destination']) {
                return response()->json(['message' => 'Mohon pilih Wallet Tujuan.'], 400);
            }
            // Save the source first
            $request['value'] = $request['value'] * -1;
            new TransactionResource(Transaction::create($request->all()));
            // Seve the destination next
            $request['value'] = $request['value'] * -1;
            $request['user_wallet_id'] = $request['user_wallet_id_destination'];
        }
        $request['created_at'] = Carbon::now();
        new TransactionResource(Transaction::create($request->all()));
        return response()->json(['message' => 'Success'], 200);
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
