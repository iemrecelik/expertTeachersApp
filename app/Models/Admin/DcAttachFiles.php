<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcAttachFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_att_file_path',
    ];

    /**
     * Get the parent dcAttachFiles model (post or video).
     */
    public function dcAttachFiles()
    {
        return $this->morphTo();
    }
}
