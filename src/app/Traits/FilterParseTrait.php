<?php

namespace Mmstfkc\BasicCrud\app\Traits;

trait FilterParseTrait
{

    function parseFilterData(string|array $rawText, string $parseChar = ':'): array
    {
        $data = [];

        if (is_string($rawText)) {
            $rawText = [$rawText];
        }

        foreach ($rawText as $key => $d) {

            if ($parsedData = strpos($key, $parseChar)) {
                $value = substr($key, ($parsedData + 1));

                if (in_array($value, ['true', 'false'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }

                if (is_numeric($value)) {
                    $value = floor($value) == $value ? (int)$value : (float)$value;
                }

                $data[substr($key, 0, $parsedData)] = $value;
            }
        }

        return $data;
    }
}
