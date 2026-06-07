<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends BusinessException
{
    protected int $statusCode = 422;
    protected $message ='Insufficient Balance';
}