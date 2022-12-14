<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Illuminate\Support\Facades\DB;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        $modelName = $this->route()->getController()->modelName;
        $model = app()->make($modelName);
        $columns = $this->getColumnName($modelName, 'store');
        $tableName = $model->getTable();
        $columnTypes = DB::table('information_schema.columns')->where('table_name', $tableName)
            ->select(
                'column_name',
                'is_nullable',
                'data_type',
                'character_maximum_length',
                'udt_name',
                'numeric_precision',
                'numeric_precision_radix',
                'numeric_scale',)
            ->get();

        $rules = [];

        foreach ($columns as $column) {
            $rule = [];
            foreach ($columnTypes as $columnType) {
                if ($column == data_get($columnType, 'column_name')) {
                    $rule[] = $columnType->is_nullable == 'YES' ? 'nullable' : 'required';
                    $rule[] = $columnType->udt_name;

                    if ($columnType->character_maximum_length) {
                        $rule[] = 'max:' . $columnType->character_maximum_length;
                    }

                    if ($columnType->udt_name == 'numeric') {
                        $max = pow(10, $columnType->numeric_precision);
                        $min = pow(10, -$columnType->numeric_scale);
                        $rule[] = 'between:' . $min . ',' . $max - $min;
                    }
                }
            }
            $rules[$column] = $rule;
        }

        return $rules;
    }
}
