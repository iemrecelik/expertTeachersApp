<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LawsuitFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'lawf_file_path',
        'lawf_file_name',
        'law_id',
        'dc_id',
    ];
}
