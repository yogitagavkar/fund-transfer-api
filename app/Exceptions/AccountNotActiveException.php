<?php

namespace App\Exceptions;

use Exception;

class AccountNotActiveException extends BusinessException
{
    protected $message ='Account is not active';
    protected int $statusCode = 404;
}