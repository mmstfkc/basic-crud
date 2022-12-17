<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Illuminate\Contracts\Container\BindingResolutionException;

class UpdateRequest extends BaseRequest
{
    /**
     * @return array
     * @throws BindingResolutionException
     */
    public function rules(): array
    {
        return $this->getRules('update', $this->getColumns(), $this->getColumnTypes());
    }
}
