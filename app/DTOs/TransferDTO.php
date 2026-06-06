<?php

namespace App\DTOs;

readonly class TransferDTO
{
    public function __construct(
        public int $fromAccountId,
        public int $toAccountId,
        public string $amount,
        public ?string $description,
        public ?string $referenceId
    ) {}
}