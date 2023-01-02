<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\InstitutionsRepository;
use Illuminate\Database\Eloquent\Model;

class Institutions extends Model
{
    use HasFactory;
    
    use InstitutionsRepository;

    protected $fillable = [
        'inst_name',
    ];
}
