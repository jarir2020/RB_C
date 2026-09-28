<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    protected $fillable = ['ledger_account_id', 'branch_id', 'entry_date', 'reference', 'description', 'debit', 'credit', 'status'];

    protected $casts = ['entry_date' => 'date', 'debit' => 'decimal:2', 'credit' => 'decimal:2'];

    public function account(): BelongsTo { return $this->belongsTo(LedgerAccount::class, 'ledger_account_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
