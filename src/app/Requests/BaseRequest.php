<?php

namespace Mmstfkc\BasicCrud\app\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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

    protected function getColumnName(string $modelName, string $functionName)
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
            return $allColumnNames;
        } else {

            return [1];
        }
    }
}
