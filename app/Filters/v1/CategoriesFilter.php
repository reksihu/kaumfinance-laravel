<?php

namespace App\Filters\v1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class CategoriesFilter extends ApiFilter {
    protected $safeParms = [
        'id' => ['eq'],
        'category' => ['eq'],
        'transactionTypeId' => ['eq'],
    ];
    
    protected $columnMap = [
        'transactionTypeId' => 'transaction_type_id',
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
            
            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;
            
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }
}
