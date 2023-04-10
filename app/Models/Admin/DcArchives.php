<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcArchives extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_arc_number',
        'dc_arc_date',
    ];
}
