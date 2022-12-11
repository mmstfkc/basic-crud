<?php

namespace Mmstfkc\BasicCrud\Rules;

use Illuminate\Contracts\Validation\Rule;

class MultipleFilter implements Rule
{
    protected array $keyText;
    protected array|null $valueText;
    protected string $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $keyText, array $valueText = null)
    {
        $this->keyText = $keyText;
        $this->valueText = $valueText;

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
        if (is_array($value)) {
            $this->errorMessage = trans('please_enter_a_valid_key_or_value');
            return false;
        }

        [$str, $key, $value] = $this->parseFilterData($value);

        if ($str) {
            if (in_array($key, $this->keyText)) {
                if ($this->valueText) {
                    if (in_array($value, $this->valueText)) {
                        return true;
                    }
                    $this->errorMessage = trans('please_enter_a_valid_key_or_value');

                    return false;
                }

                return true;
            }

            $this->errorMessage = trans('please_enter_a_valid_key_or_value');
            return false;
        }

        $this->errorMessage = trans('please_write_the_entered_data_as_key:value');
        return false;
    }

    function parseFilterData(string|array $rawText): array
    {
        if (is_string($rawText)) {
            $rawText = [$rawText];
        }

        $data = [];

        foreach ($rawText as $text) {
            if ($parsedData = strpos($text, config('basicCrud.parse_character'))) {
                $value = substr($text, ($parsedData + 1));

                if (in_array($value, ['true', 'false'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }

                if (is_numeric($value)) {
                    $value = floor($value) == $value ? (int)$value : (float)$value;
                }

                $data[substr($text, 0, $parsedData)] = $value;
            }
        }

        return $data;
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
