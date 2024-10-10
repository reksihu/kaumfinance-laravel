<?php

namespace App\Filters\v1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class TransactionsFilter extends ApiFilter {
    protected $safeParms = [
        'id' => ['eq'],
        'date' => ['eq'],
        'transactionTypeId' => ['eq'],
        // Cannot filtering by name because different table, you better use the Id
        // 'transactionTypeName' => ['eq'],
        'userWalletId' => ['eq'],
        'value' => ['eq', 'gt', 'gte', 'lt', 'lte'],
        'category' => ['eq'],
        'subCategory' => ['eq']
    ];
    
    // Only to mapping text transactionTypeId to db format text which is transaction_type_id
    protected $columnMap = [
        'transactionTypeId' => 'transaction_type_id',
        'userWalletId' => 'user_wallet_id',
        'subCategory' => 'sub_category'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];

    public function transform(Request $request) {
        $eloQuery = [];

        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);
            
            // check if it is not null
            if (!isset($query)) {
                continue;
            }

            // to convert special format param
            $column = $this->columnMap[$parm] ?? $parm;
            
            // to handle the multiple operator
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    // the result for filter query
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }
}
