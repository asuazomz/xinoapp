<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'allow_multiple_per_month',
        'is_active',
    ];
}