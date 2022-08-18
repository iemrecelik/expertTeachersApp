<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcCatCat extends Model
{
    use HasFactory;

    protected $table = 'dc_cat_cat';

    public function dcCategory()
    {
        return $this->belongsTo('App\Models\Admin\DcCategory', );
    }
}
