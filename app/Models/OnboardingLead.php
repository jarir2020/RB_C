<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingLead extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'business_name', 'business_type', 'branch_count', 'status'];
    protected $casts = ['branch_count' => 'integer'];
}
