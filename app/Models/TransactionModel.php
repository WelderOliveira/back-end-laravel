<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionModel extends Model
{
    use HasFactory;

    protected $table = 'transaction';

    protected $fillable = [
        'value',
        'payee_id',
        'payer_id'
    ];
}
