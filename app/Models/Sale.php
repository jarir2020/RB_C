<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = ['invoice_no', 'contact_id', 'branch_id', 'sold_at', 'total', 'status', 'channel'];

    protected $casts = ['sold_at' => 'date', 'total' => 'decimal:2'];

    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function items(): HasMany { return $this->hasMany(SaleItem::class); }
    public function returns(): HasMany { return $this->hasMany(SaleReturn::class); }
}
