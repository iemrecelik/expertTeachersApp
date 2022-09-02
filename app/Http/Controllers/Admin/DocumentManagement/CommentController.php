<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\DcDocuments;
use App\Models\Admin\DcComment;

class CommentController extends Controller
{
    public function addComment(Request $request)
    {

        /* 
            1- Bütün formlarda validation kontrolleri yapılacak 
            2- Dökümanların ekleri gösterilecek ve resim , pdf dosyaları tarayıcı gösterilecek
            3- udf dosyası indirilebilir olacak
            4- 
        */

        $params = $request->all();

        $dcComment = DcComment::where([
            ['dc_id', $params['dc_id']],
            ['user_id', $request->user()->id]
        ]);

        dump($dcComment);die;

        if($dcComment->isNotEmpty()) {

            $dcComment->dc_com_text = $params['dc_com_text'];
            $dcComment->save();

        }else {
            $dcComment = DcComment::create([
                'dc_com_text'   => $params['dc_com_text'],
                'dc_id'         => $params['dc_id'],
                'user_id'       => $request->user()->id,
            ]);
        }

        return ['succeed' => __('messages.add_success')];
    }
}
