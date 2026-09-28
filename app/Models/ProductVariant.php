<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'barcode', 'attributes', 'price', 'stock', 'reorder_level', 'status'];
    protected $casts = ['attributes' => 'array', 'price' => 'decimal:2', 'stock' => 'integer', 'reorder_level' => 'integer'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
