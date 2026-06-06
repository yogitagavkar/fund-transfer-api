<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_number',
        'account_holder_name',
        'balance',
        'status'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(
            Transaction::class,
            'from_account_id'
        );
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(
            Transaction::class,
            'to_account_id'
        );
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
