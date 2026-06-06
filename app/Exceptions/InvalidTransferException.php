<?php

namespace App\Exceptions;

use Exception;

class InvalidTransferException extends BusinessException
{
     protected int $statusCode = 422;
     protected $message ='Invalid Transaction';
}