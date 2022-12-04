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
        'law_order',
        'law_brief',
        'dc_id',
        'uns_id',
        'thr_id',
        'law_id',
        'sub_id',
    ];

    /**
     * Get the teacher.
     */
    public function teacher()
    {
        return $this->hasOne(Teachers::class, 'id', 'thr_id');
    }
    
    /**
     * Get the union.
     */
    public function union()
    {
        return $this->hasOne(Unions::class, 'id', 'uns_id');
    }

    /**
     * Get the dc_document.
     */
    public function dc_document()
    {
        return $this->hasOne(DcDocuments::class, 'id', 'dc_id');
    }

    /**
     * The dc_lawsuits that belong to the dc_documents.
     */
    public function dc_documents()
    {
        return $this->belongsToMany(DcDocuments::class, 'law_dc', 'law_id', 'dc_id');
    }
    
    /**
     * The dc_lawsuits that belong to the subjects.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subjects::class, 'law_sub', 'law_id', 'sub_id');
    }

    /**
     * The files that belong to the lawsuit.
     */
    public function lawsuitFiles()
    {
        return $this->hasMany(LawsuitFiles::class, 'law_id', 'id');
    }
}
