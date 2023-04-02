<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'set_auth_signature_names',
        'set_ip_names',
        'set_allow_file_ext',
    ];

    protected $table = 'settings';
}
