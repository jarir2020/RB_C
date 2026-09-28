<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'barcode', 'category', 'brand', 'unit', 'price', 'cost_price', 'wholesale_price', 'price_label', 'description', 'stock', 'reorder_level', 'status'];

    protected $casts = ['price' => 'decimal:2', 'cost_price' => 'decimal:2', 'wholesale_price' => 'decimal:2', 'stock' => 'integer', 'reorder_level' => 'integer'];

    public function getStockStatusAttribute(): string { return $this->stock <= $this->reorder_level ? 'Low stock' : $this->status; }
    public function saleItems(): HasMany { return $this->hasMany(SaleItem::class); }
    public function branchStocks(): HasMany { return $this->hasMany(BranchStock::class); }
    public function returns(): HasMany { return $this->hasMany(SaleReturn::class); }
    public function transfers(): HasMany { return $this->hasMany(StockTransfer::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
}
