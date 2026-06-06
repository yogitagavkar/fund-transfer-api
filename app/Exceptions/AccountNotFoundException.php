<?php

namespace App\Exceptions;

use Exception;

class AccountNotFoundException extends BusinessException
{
    protected $message ='Account not found';
    protected int $statusCode = 404;
}