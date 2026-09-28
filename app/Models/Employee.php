<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = ['branch_id', 'name', 'role', 'email', 'phone', 'joined_on', 'status'];

    protected $casts = ['joined_on' => 'date'];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
}
