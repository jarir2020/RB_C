<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturn extends Model
{
    protected $table = 'sales_returns';

    protected $fillable = ['return_no', 'sale_id', 'contact_id', 'branch_id', 'product_id', 'returned_on', 'quantity', 'refund_amount', 'reason', 'status'];

    protected $casts = ['returned_on' => 'date', 'quantity' => 'integer', 'refund_amount' => 'decimal:2'];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
