<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\ModelsRepository\Admin\DcCategoryRepository;
use Illuminate\Database\Eloquent\Model;

class DcCategory extends Model
{
    use HasFactory;
    
    use DcCategoryRepository;

    protected $fillable = [
        'dc_cat_name'
    ];

    protected $table = 'dc_category';

    public function childCategory()
    {
        return $this->hasMany(DcCategory::class, 'dc_cat_id', 'id');
    }
    public function parentChild()
    {
        return $this->belongsTo(DcCategory::class, 'id', 'dc_cat_id');
    }
}
