<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_file_path',
    ];

    /**
     * Get the parent dcFile model (user or post).
     */
    public function dcFiles()
    {
        return $this->morphTo();
    }
}
