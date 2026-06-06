<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends BusinessException
{
    protected int $statusCode = 400;
    protected $message ='Insufficient Balance';
}