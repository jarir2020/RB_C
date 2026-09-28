<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'city', 'sales_target', 'status'];

    protected $casts = ['sales_target' => 'decimal:2'];

    public function sales(): HasMany { return $this->hasMany(Sale::class); }
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function warehouses(): HasMany { return $this->hasMany(Warehouse::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function branchStocks(): HasMany { return $this->hasMany(BranchStock::class); }
    public function outgoingTransfers(): HasMany { return $this->hasMany(StockTransfer::class, 'from_branch_id'); }
    public function incomingTransfers(): HasMany { return $this->hasMany(StockTransfer::class, 'to_branch_id'); }
    public function ledgerEntries(): HasMany { return $this->hasMany(LedgerEntry::class); }
}
