<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bom extends Model
{
    protected $fillable = ['product_id', 'name', 'output_quantity', 'status', 'notes'];
    protected $casts = ['output_quantity' => 'integer'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function items(): HasMany { return $this->hasMany(BomItem::class); }
    public function productionOrders(): HasMany { return $this->hasMany(ProductionOrder::class); }
}
