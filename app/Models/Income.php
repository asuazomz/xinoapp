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
    ];

    public function member()
    {
        return $this->belongsTo(HouseholdMember::class, 'household_member_id');
    }
}