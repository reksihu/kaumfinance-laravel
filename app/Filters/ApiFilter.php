<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter {
    protected $safeParms = [];

    protected $columnMap = [];

    protected $operatorMap = [];

    public function transform(Request $request) {
        $eloQuery = [];

        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);
            
            // Check if it is not null
            if (!isset($query)) {
                continue;
            }

            // To convert special format param
            $column = $this->columnMap[$parm] ?? $parm;
            
            // To handle the multiple operator
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    // The result for filter query
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }
}
