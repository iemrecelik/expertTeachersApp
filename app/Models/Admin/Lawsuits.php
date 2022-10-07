<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\LawsuitsRepository;
use Illuminate\Database\Eloquent\Model;

class Lawsuits extends Model
{
    use HasFactory;
    
    use LawsuitsRepository;

    protected $fillable = [
        'uns_name',
        'law_order',
        'dc_id',
        'uns_id',
        'thr_id',
        'law_id',
        'sub_id',
    ];
}
