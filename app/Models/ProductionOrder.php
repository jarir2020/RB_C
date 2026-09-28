<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrder extends Model
{
    protected $fillable = ['bom_id', 'branch_id', 'order_no', 'planned_on', 'output_quantity', 'status', 'notes'];
    protected $casts = ['planned_on' => 'date', 'output_quantity' => 'integer'];
    public function bom(): BelongsTo { return $this->belongsTo(Bom::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
