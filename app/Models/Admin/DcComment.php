<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\ModelsRepository\Admin\DcCommentRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DcComment extends Model
{
    use HasFactory;

    use DcCommentRepository;

    protected $table = 'dc_comment';

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
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    /**
     * Get the dc_id.
     */
    public function dc_document()
    {
        return $this->hasOne(DcDocument::class, 'dc_id', 'id');
    }
}
