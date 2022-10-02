<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_item_status',
        'dc_main_status',
        'dc_cat_id',
        'dc_number',
        'dc_subject',
        'dc_who_send',
        'dc_who_receiver',
        'dc_subject',
        'dc_content',
        'dc_raw_content',
        'dc_show_content',
        'dc_date',
        'dc_manuel',
        'user_id',
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

    /**
     * The dc_relatives that belong to the dc_document.
     */
    public function dc_ralatives()
    {
        return $this->belongsToMany(DcDocuments::class, 'dc_relative', 'dc_id', 'rel_id');
    }

    /**
     * The dc_teachers that belong to the dc_document.
     */
    public function dc_teachers()
    {
        return $this->belongsToMany(Teachers::class, 'dc_thr', 'dc_id', 'thr_id');
    }


    /**
     * The dc_list that belong to the dc_document.
     */
    public function dc_lists()
    {
        return $this->belongsToMany(DcLists::class, 'dc_doc_list', 'dc_id', 'list_id');
    }

    /**
     * Get the document's file.
     */
    public function dc_user_pers()
    {
        return $this->morphMany(User::class, 'dc_per_owner');
    }
}
