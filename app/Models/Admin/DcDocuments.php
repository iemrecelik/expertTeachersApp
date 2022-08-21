<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_item_status',
        'dc_cat_id',
        'dc_number',
        'dc_subject',
        'dc_who_send',
        'dc_who_receiver',
        'dc_subject',
        'dc_content',
        'dc_raw_content',
        'dc_date',
    ];

    /**
     * Get the document's file.
     */
    public function dcFiles()
    {
        return $this->morphOne(DcFiles::class, 'dc_file_owner');
    }

    /**
     * Get all of the document's comments.
     */
    public function dcAttachFiles()
    {
        return $this->morphMany(DcAttachFiles::class, 'dc_att_file_owner');
    }
}
