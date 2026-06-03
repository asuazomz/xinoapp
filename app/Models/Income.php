<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'person_name',
        'amount',
        'month',
        'description',
    ];
}