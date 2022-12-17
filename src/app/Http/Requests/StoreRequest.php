<?php

namespace Mmstfkc\BasicCrud\app\Http\Requests;

use Illuminate\Contracts\Container\BindingResolutionException;

class StoreRequest extends BaseRequest
{
    /**
     * @return array
     * @throws BindingResolutionException
     */
    public function rules(): array
    {
        return $this->getRules('store');
    }
}
