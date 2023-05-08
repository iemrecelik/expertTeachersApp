<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\ModelsRepository\Admin\RolesRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    use HasFactory;
    use RolesRepository;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
        'nickname',
    ];
}
