<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin\DcDocuments;
use App\Models\Admin\DcFiles;
use App\Models\Admin\DcAttachFiles;
use App\Library\MebBot\DysWebBot;
use App\Http\Controllers\Admin\DocumentManagement\DocumentsController;

class DcWaitingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'admin.document_mng.waiting.index'
        );
    }
    

    private function saveDocumentsToDb($result)
    {
        foreach ($result as $key => $val) {
            $documentsController = new DocumentsController();
            $content = $documentsController->getBotFileInfos($val['dc_file_path']);

            // $content = $this->changeTurkishCharecter($content);
            
            $result[$key]['dc_content'] = $content;
        }

        foreach ($result as $key => $val) {
            $user = auth()->user();

            $val['user_id'] = $user->id;

            $attFilePaths = $val['dc_att_file_path'] ?? [];
            $filePath = $val['dc_file_path'];
            unset($val['dc_file_path']);
            unset($val['dc_att_file_path']);

            $val['dc_show_content'] = '';
            $val['dc_raw_content'] = '';
            
            $dc = DcDocuments::create($val);

            $dcFiles = new DcFiles(
                [
                    'dc_file_path' => $filePath
                ]
            );

            $dc->dcFiles()->saveMany([$dcFiles]);

            if(count($attFilePaths) > 0 ) {
                $dcAttachFiles = array_map(function($path) {
                    return new DcAttachFiles([
                            'dc_att_file_path' => $path
                        ])
                    ;
                }, $attFilePaths);

                $dc->dcAttachFiles()->saveMany($dcAttachFiles);
            } 
        }
    }

    public function saveBotDocument(Request $request)
    {
        $params = $request->all();

        // dd($params);

        $dysBot = new DysWebBot();
        // $result = $dysBot->getDocuments($params['date'], strval($params['item_status']));
        $result = $dysBot->getDocuments('10-06-2022', strval($params['item_status']));

        if(count($result) > 0) {
            $this->saveDocumentsToDb($result);
        }

        return ['succeed' => __('messages.add_success')];

        /* return view(
            'admin.document_mng.waiting.index',
            ['succeed' => __('messages.add_success')]
        ); */
    }

    public function getWaitingDocument(Request $request)
    {
        $tblInfo = $request->all();

        $notSelectCol = [
            'user_name',
        ];

	    /*Array select and search columns*/
	    foreach ($tblInfo['columns'] as $column) {

            if (isset($column['data'])){
                if(!in_array($column['data'], $notSelectCol))
                    $selectCol[] = $column['data'];
            }

	        if($column['searchable'])
	            $searchCol[] = $column['data'];
	    }

	    /*Order settings*/
	    $colIndex = $tblInfo['order'][0]['column'];
	    $colOrder = $tblInfo['columns'][$colIndex]['data'];
	    $order = $tblInfo['order'][0]['dir'];

        /*join*/
        $join = [
            "users as t1", 
            "t0.user_id", '=', 
            "t1.id"
        ];

        $selectJoin = ", t1.name as user_name";
        
	    $dataList = DcDocuments::dataList([
	        'table' => 'dc_documents',
	        'fieldIDName' => 'id',
	        'addLangFields' => [],
            'choiceJoin' => 'join',
            'join' => $join,
            'selectJoin' => $selectJoin,
	        'selectCol' => $selectCol,
	        'searchCol' => $searchCol,
	        'colOrder' => $colOrder,
	        'order' => $order,
	        'search' => $tblInfo['search']['value'],
	    ]);

        $dataList->whereRaw('NOT EXISTS (
            SELECT  1
            FROM    dc_cat t1
            WHERE   t1.dc_id = t0.id
        )');

	    $recordsTotal = DcDocuments::count();
	    $recordsFiltered = $dataList->count();
        
	    $data = $dataList->offset($tblInfo['start'])
	    ->limit($tblInfo['length'])
	    ->get();
        
	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $data,
	        'draw' => $tblInfo['draw']
	    ];

        return $datas;
    }
}
