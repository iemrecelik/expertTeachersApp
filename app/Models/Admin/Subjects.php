<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\SubjectsRepository;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;
    
    use SubjectsRepository;

    protected $fillable = [
        'sub_order',
        'sub_description',
        'law_id',
    ];
}
