<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'accounts';
    protected $fillable = [
        'value'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_id', 'id');
    }
}
