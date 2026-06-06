<?php
namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected int $statusCode = 400;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}