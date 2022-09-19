<?php

namespace App\Models\Admin\OldRegulation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OldFirstColApp extends Model
{
    use HasFactory;

    protected $table = 'old_first_col_app';

    public function getSearchList(Array $info) 
    {
        $datas = DB::table('old_first_col_app')
        // ->select('*')
        ->whereRaw($info['where'])
        ->get();

        return $datas;
    }
}
