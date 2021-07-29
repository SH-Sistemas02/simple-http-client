<?php
namespace Simple\Exceptions;

use Exception;

class FailedExecutionException extends Exception
{
    public function report()
    {
        return true;
    }
}