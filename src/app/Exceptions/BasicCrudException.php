<?php

namespace Mmstfkc\BasicCrud\app\Exceptions;

use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class BasicCrudException extends Exception
{
    protected array $errors;
    protected $message;
    protected $code;

    /**
     * BasicCrudValidationException constructor.
     * @param $message
     * @param null $code
     * @param Throwable|null $previous
     * @param array $errors
     */
    public function __construct($message, $code = null, Throwable $previous = null, $errors = [])
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;
        $this->errors = $errors;
        $this->code = Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
