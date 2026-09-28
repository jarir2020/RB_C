<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = ['order_no', 'contact_id', 'branch_id', 'ordered_at', 'total', 'status', 'items_count'];

    protected $casts = ['ordered_at' => 'date', 'total' => 'decimal:2', 'items_count' => 'integer'];

    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
