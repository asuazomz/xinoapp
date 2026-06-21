<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\HouseholdMember;

class Income extends Model
{
    protected $fillable = [
        'household_member_id',
        'person_name',
        'amount',
        'month',
        'description',
        'income_category_id',
    ];

    public function member()
    {
        return $this->belongsTo(HouseholdMember::class, 'household_member_id');
    }

    public function category()
    {
    return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }
    
}