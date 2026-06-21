<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'category',
        'month',
        'description',
    ];
}