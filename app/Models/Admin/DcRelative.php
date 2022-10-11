<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcRelative extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_id',
        'rel_id'
    ];

    protected $table = 'dc_relative';
}
