<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = ['name', 'bank', 'account_no', 'type', 'opening_balance', 'balance', 'status'];
    protected $casts = ['opening_balance' => 'decimal:2', 'balance' => 'decimal:2'];
    public function cheques(): HasMany { return $this->hasMany(Cheque::class); }
}
