<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
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
}
