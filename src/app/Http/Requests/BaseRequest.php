<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * @param string $modelName
     * @param string $functionName
     * @return array
     * @throws BindingResolutionException
     */
    protected function getColumnName(string $modelName, string $functionName): array
    {
        return $this->getColumnNameForPostgreSQL($modelName, $functionName);
    }

    /**
     * @param string $modelName
     * @param string $functionName
     * @return array
     * @throws BindingResolutionException
     */
    protected function getColumnNameForPostgreSQL(string $modelName, string $functionName): array
    {
        $model = app()->make($modelName);
        $allColumnNames = DB::getSchemaBuilder()->getColumnListing($model->getTable());

        if ($functionName == 'index') {
            $hiddenValues = $model->first()?->getHidden();

            if ($hiddenValues) {
                foreach ($hiddenValues as $hiddenValue) {
                    foreach ($allColumnNames as $key => $allColumnName) {
                        if ($allColumnName == $hiddenValue) {
                            unset($allColumnNames[$key]);
                        }
                    }
                }
            }
        } else {
            if ($fillable = $model->getFillable()) {
                return $fillable;
            }

            $guarded = $model->getGuarded();

            if (in_array('*', $guarded)) {
                return [];
            }

            unset($allColumnNames[array_search('id', $allColumnNames)]);

            foreach ($guarded as $guard) {
                foreach ($allColumnNames as $key => $allColumnName) {
                    if ($allColumnName == $guard) {
                        unset($allColumnNames[$key]);
                    }
                }
            }
        }

        return $allColumnNames;
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getColumnTypes(): Collection
    {
        return $this->getColumnTypesForPostgreSql();
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    protected function getColumnTypesForPostgreSql(): Collection
    {
        $modelName = $this->route()->getController()->modelName;
        $model = app()->make($modelName);


        $tableName = $model->getTable();
        return DB::table('information_schema.columns')->where('table_name', $tableName)
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
    }

    /**
     * @param string $method
     * @return array
     * @throws BindingResolutionException
     */
    protected function getRules(string $method): array
    {
        return $this->postgreSqlRule($method);
    }

    /**
     * @param string $method
     * @return array
     * @throws BindingResolutionException
     */
    protected function postgreSqlRule(string $method): array
    {
        $rules = [];

        $modelName = $this->route()->getController()->modelName;
        $columns = $this->getColumnName($modelName, 'store');
        $columnTypes = $this->getColumnTypes();

        foreach ($columns as $column) {
            $rule = [];
            foreach ($columnTypes as $columnType) {
                if ($column == data_get($columnType, 'column_name')) {
                    $rule[] = $columnType->is_nullable == 'YES' ? 'nullable' : 'required';

                    // TODO unsigned, enum

                    if (
                        $columnType->udt_name == 'varchar' ||
                        $columnType->udt_name == 'text'
                    ) {
                        $rule[] = 'string';
                    } elseif ($columnType->udt_name == 'date' || $columnType->udt_name == 'timestamp') {
                        $rule[] = 'date';
                        $rule[] = 'date_format:' . config('basicCrud.date_format');
                    } elseif (
                        $columnType->udt_name == 'json' ||
                        $columnType->udt_name == 'bool'
                    ) {
                        $rule[] = $columnType->udt_name;
                    } elseif (is_numeric(stripos($columnType->udt_name, 'int'))) {
                        $rule[] = 'integer';
                    } elseif ($columnType->udt_name == 'numeric') {
                        $max = pow(10, $columnType->numeric_precision);
                        $min = pow(10, -$columnType->numeric_scale);
                        $rule[] = 'numeric';
                        $rule[] = 'between:' . $min . ',' . $max - $min;
                    } elseif (
                        $columnType->udt_name == 'bytea' ||
                        $columnType->udt_name == 'bpchar' ||
                        $columnType->udt_name == 'macaddr' ||
                        $columnType->udt_name == 'uuid'
                    ) {
                        //TODO will be added
                    }

                    if ($columnType->character_maximum_length) {
                        $rule[] = 'max:' . $columnType->character_maximum_length;
                    }

                    // TODO uniq
                    if ($method == 'update' and $columnType->udt_name != 'json') {
                        /*
                        $modelName = $this->route()->getController()->modelName;
                        $id = $this->route()->id;

                        $rule[] = 'unique:' . $modelName . ',' . $column . ',' . $id;
                        */
                    }
                }
            }
            $rules[$column] = $rule;
        }

        return $rules;
    }
}
