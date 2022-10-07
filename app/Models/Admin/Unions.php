<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\UnionsRepository;
use Illuminate\Database\Eloquent\Model;

class Unions extends Model
{
    use HasFactory;
    
    use UnionsRepository;

    protected $fillable = [
        'uns_name',
    ];
}
