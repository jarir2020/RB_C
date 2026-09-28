<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['payment_no', 'sale_id', 'contact_id', 'branch_id', 'paid_on', 'amount', 'method', 'reference', 'status'];

    protected $casts = ['paid_on' => 'date', 'amount' => 'decimal:2'];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
