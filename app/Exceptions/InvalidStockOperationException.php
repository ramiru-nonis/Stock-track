<?php

namespace App\Exceptions;

use Exception;

class InvalidStockOperationException extends Exception
{
    public function __construct(string $message = "Invalid stock operation requested.")
    {
        parent::__construct($message, 422);
    }
}
