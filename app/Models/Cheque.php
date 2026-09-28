<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    protected $fillable = ['bank_account_id', 'contact_id', 'cheque_no', 'cheque_date', 'amount', 'direction', 'status', 'notes'];
    protected $casts = ['cheque_date' => 'date', 'amount' => 'decimal:2'];
    public function bankAccount(): BelongsTo { return $this->belongsTo(BankAccount::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
}
