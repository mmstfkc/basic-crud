<?php

namespace Mmstfkc\BasicCrud\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mmstfkc\BasicCrud\app\Traits\FilterParseTrait;

class MultipleFilter implements Rule
{
    use FilterParseTrait;

    protected array $keyText;
    protected array|null $valueText;
    protected array|null $operatorText;
    protected string $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $keyText, array $valueText = null, array $operatorText = null)
    {
        $this->keyText = $keyText;
        $this->valueText = $valueText;
        $this->operatorText = $operatorText;

        if (is_null($operatorText)) {
            $this->operatorText = ['<', '>', '<=', '>=', '=', '!='];
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($operator = data_get($value, config('basicCrud.filter_operator_key'))) {
            if (!in_array($operator, $this->operatorText)) {
                $this->errorMessage = trans('operator_is_not_defined');
                return false;
            }
        }

        foreach ($value as $key => $item) {
            if (in_array($key, $this->keyText) || $key === config('basicCrud.filter_operator_key')) {

                if ($this->valueText) {
                    if (in_array($value, $this->valueText)) {
                        return true;
                    }
                    $this->errorMessage = trans('please_enter_a_valid_key_or_value');

                    return false;
                }

                return true;
            }
        }

        $this->errorMessage = trans('please_enter_a_valid_key_or_value');
        return false;
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage;
    }
}
