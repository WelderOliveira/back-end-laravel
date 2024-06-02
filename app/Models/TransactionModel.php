<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionModel extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'value',
        'payee_id',
        'payer_id'
    ];

    /**
     * @return BelongsTo
     */
    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id', 'id');
    }
}
