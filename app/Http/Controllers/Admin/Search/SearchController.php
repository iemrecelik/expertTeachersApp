<?php

namespace App\Http\Controllers\Admin\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function showTeacherInfos()
    {
        /* $path = Storage::disk('public')->path('udf/belge.udf');
        $pathTo = Storage::disk('public')->path('');
        dump($pathTo);die;
        dump($path);die;

        $zip = new \ZipArchive;
  
        // Zip File Name
        if ($zip->open($path) === TRUE) {        
            // Unzip Path
            $zip->extractTo($pathTo);
            $zip->close();
            echo 'Unzipped Process Successful!';
        } else {
            echo 'Unzipped Process failed';
        }
die('SON'); */

        // $content = File::get(storage_path('udf/test.txt'));
        // $content = Storage::disk('public')->get('udf/belge.udf');
        /* $content = Storage::disk('public')->get('deneme.xml');

        dump($content);die; */
        return view('admin.search.teacher_infos');
    }
}
