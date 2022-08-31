<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dc_com_text',
        'user_id',
        'dc_id',
    ];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
    
    /**
     * Get the dc_id.
     */
    public function dc_document()
    {
        return $this->hasOne(DcDocument::class, 'dc_id', 'id');
    }
}
