<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Mmstfkc\BasicCrud\Rules\MultipleFilter;

class IndexRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $column = $this->getColumnName($this->route()->getController()->modelName, 'index');

        return [
            'page' => 'required_with:limit|integer|min:1',
            'limit' => 'required_with:page|integer|min:1|max:' . config('basicCrud.max_limit'),
            'order_by' => 'nullable|array',
            'order_by.*' => ['nullable', 'string', new MultipleFilter($column, ['asc', 'desc'])],
            'where' => 'nullable|array',
            'where.*' => ['nullable', 'string', 'min:1', new MultipleFilter($column)],
            'like' => 'nullable|array',
            'like.*' => ['nullable', 'string', new MultipleFilter($column)],
        ];
    }
}
