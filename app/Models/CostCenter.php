<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $fillable = ['name', 'code', 'budget', 'spent', 'status'];
    protected $casts = ['budget' => 'decimal:2', 'spent' => 'decimal:2'];
}
