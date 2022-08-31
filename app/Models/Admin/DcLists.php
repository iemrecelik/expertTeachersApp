<?php

namespace App\Models\Admin;

use App\ModelsRepository\Admin\DcListsRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcLists extends Model
{
    use HasFactory;
    use DcListsRepository;

    protected $fillable = [
        'dc_list_name',
        'user_id',
    ];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    /**
     * The dc_document that belong to the dc_list.
     */
    public function dc_documents()
    {
        return $this->belongsToMany(DcDocuments::class, 'dc_doc_list', 'dc_id', 'list_id');
    }
}
