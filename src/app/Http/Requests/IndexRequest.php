<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Mmstfkc\BasicCrud\Rules\MultipleFilter;

class IndexRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws BindingResolutionException
     */
    public function rules(): array
    {
        $column = $this->getColumnName($this->route()->getController()->modelName, 'index');

        return [
            'page' => 'required_with:limit|integer|min:1',
            'limit' => 'required_with:page|integer|min:1|max:' . config('basicCrud.max_limit'),
            'order_by' => 'nullable|array',
            'order_by.*' => ['nullable', new MultipleFilter($column, ['asc', 'desc'])],

            'where' => 'nullable|array',
            'where.*' => ['nullable', new MultipleFilter($column)],
            'where_in' => 'nullable|array',
            'where_in.*' => ['nullable', new MultipleFilter($column)],
            'where_not_in' => 'nullable|array',
            'where_not_in.*' => ['nullable', new MultipleFilter($column)],

            'like' => 'nullable|array',
            'like.*' => ['nullable', new MultipleFilter($column)],

            'ilike' => 'nullable|array',
            'ilike.*' => ['nullable', new MultipleFilter($column)],

        ];
    }
}
