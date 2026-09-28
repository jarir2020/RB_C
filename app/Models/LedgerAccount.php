<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LedgerAccount extends Model
{
    protected $fillable = ['code', 'name', 'type', 'balance', 'trend'];

    protected $casts = ['balance' => 'decimal:2'];

    public function entries(): HasMany { return $this->hasMany(LedgerEntry::class); }
}
