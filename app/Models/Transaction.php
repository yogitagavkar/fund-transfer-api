<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'reference_id',
        'description',
        'status',
        'error_message'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'from_account_id'
        );
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'to_account_id'
        );
    }
}
