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
        return $this->hasMany(Institutions::class, 'id', 'inst_id');
    }

    /**
     * The dc_documents that belong to the dc_document.
     */
    public function dc_documents()
    {
        return $this->belongsToMany(DcDocuments::class, 'dc_thr', 'thr_id', 'dc_id');
    }
}
