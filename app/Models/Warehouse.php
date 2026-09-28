<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    protected $fillable = ['branch_id', 'name', 'code', 'capacity', 'utilization', 'status'];

    protected $casts = ['capacity' => 'integer', 'utilization' => 'integer'];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
