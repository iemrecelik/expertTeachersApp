<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Towns extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'twn_name',
        'prv_id',
    ];
}
