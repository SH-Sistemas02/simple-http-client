<?php
namespace Simple\Utils;

use Simple\Exceptions\FailedExecutionException;

class FailToken
{
    private $isThrowable;

    /**
     * Crea una instancia de tipo FailToken e 
     * indica si se arroja una excepción tras un error.
     */
    public static final function doThrowOnFail()
    {
        $token = new self;
        $token->throwOnFail();

        return $token;
    }

    public function __construct() {
        $this->isThrowable = false;
    }

    /**
     * Indica si se arroja una excepción tras un error.
     */
    public function throwOnFail()
    {
        $this->isThrowable = true;
    }

    /**
     * Arroja una excepción si se indicó throwOnFail previamente.
     */
    public function fail(string $failure, int $code)
    {
        if($this->isThrowable)
        {
            throw new FailedExecutionException($failure, $code);
        }
    }

    /**
     * Arroja una excepción indistintamente si se indicó throwOnFail.
     */
    public function throw_n(string $failure, int $code)
    {
        throw new FailedExecutionException($failure, $code);
    }
}