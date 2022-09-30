<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\TeachersRepository;
use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    use HasFactory;
    
    use TeachersRepository;

    protected $fillable = [
        'thr_tc_no',
        'thr_name',
        'thr_surname',
        'thr_career_ladder',
        'thr_degree',
        'thr_task',
        'thr_education_st',
        'thr_mobile_no',
        'thr_place_of_task',
        'thr_gender',
        'thr_photo',
        'inst_id',
    ];

    protected $table = 'teachers';

    public function institution()
    {
        return $this->hasMany(Institutions::class, 'inst_id', 'id');
    }
}
