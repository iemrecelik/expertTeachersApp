<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin\DcReport;
use App\Models\Admin\DcDocuments;
use App\Http\Requests\Admin\DocumentManagement\StoreDocumentRecordCountRequest;

class DcReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sum = 0;
        $users = User::all()->toArray();

        $dateNow = strtotime(date('d.m.y'));

        foreach ($users as $userKey => $userVal) {
            $users[$userKey]['rpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $dateNow
            ])
            ->count();

            $sum += strval($users[$userKey]['rpCount']);
        }

        return view(
            'admin.document_mng.report.index',
            [
                'datas' => [
                    'users' => $users,
                    'sum' => $sum
                ]
            ]
        );
    }

    /**
     * Store a newly manual created resource in storage.
     *
     * @param  StoreDocumentRecordCountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function saveDocumentRecordCount(StoreDocumentRecordCountRequest $request)
    {
        $params = $request->all();
        $sum = 0;

        foreach ($params['user_id'] as $paramKey => $paramVal) {
            $datas = [
                'rp_date' => strtotime($params['rp_date']),
                'rp_count' => $params['rp_count'][$paramKey] ?? 0,
                'user_id' => $params['user_id'][$paramKey]
            ];

            $sum += strval($datas['rp_count']);

            $res = DcReport::where([
                'rp_date' => $datas['rp_date'],
                'user_id' => $datas['user_id']
            ])->update(['rp_count' => $datas['rp_count']]);

            if(!$res) {
                $res = DcReport::create($datas);
            }
        }

        return [
            'sum' => $sum,
        ];
    }

    /**
     * Store a newly manual created resource in storage.
     *
     * @param  StoreDocumentRecordCountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getDocumentOnDate(Request $request)
    {
        $tblInfo = $request->all();

        $rpDate = $tblInfo['rpDate'];

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

        $dataList->where('dc_date', strtotime($rpDate));

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
    }
}
