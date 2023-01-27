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
        /* $incomingSum = 0;
        $senderSum = 0;
        $users = User::all()->toArray();

        $dateNow = strtotime(date('d.m.y'));

        foreach ($users as $userKey => $userVal) {
            $users[$userKey]['incomingRpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $dateNow,
                'dc_item_status' => "1"
            ])
            ->count();

            $users[$userKey]['senderRpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $dateNow,
                'dc_item_status' => "0"
            ])
            ->count();

            $incomingSum += strval($users[$userKey]['incomingRpCount']);
            $senderSum += strval($users[$userKey]['senderRpCount']);
        }

        return view(
            'admin.document_mng.report.index',
            [
                'datas' => [
                    'users' => $users,
                    'incomingSum' => $incomingSum,
                    'senderSum' => $senderSum
                ]
            ]
        ); */


        $date = strtotime(date('d.m.y'));

        $incomingSum = 0;
        $senderSum = 0;
        $users = User::all()->toArray();

        foreach ($users as $userKey => $userVal) {
            /* Gelen başla */
            $inRp = DcReport::where([
                'user_id' => $userVal['id'],
                'rp_date' => $date,
                'rp_item_status' => "0"
            ])
            ->first();

            $users[$userKey]['incomingRpCount'] = $inRp ? $inRp['rp_count'] : 0;

            $incomingSum += strval($users[$userKey]['incomingRpCount']);
            /* Gelen bitiş */
            
            /* Gönderilen başla */
            $senRp = DcReport::where([
                'user_id' => $userVal['id'],
                'rp_date' => $date,
                'rp_item_status' => "1"
            ])
            ->first();

            $users[$userKey]['senderRpCount'] = $senRp ? $senRp['rp_count'] : 0;

            $senderSum += strval($users[$userKey]['senderRpCount']);
            /* Gönderilen bitiş */
        }



        $uincomingSum = 0;
        $usenderSum = 0;

        foreach ($users as $userKey => $userVal) {
            $users[$userKey]['uincomingRpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $date,
                'dc_item_status' => "1"
            ])
            ->count();

            $users[$userKey]['usenderRpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $date,
                'dc_item_status' => "0"
            ])
            ->count();

            $uincomingSum += strval($users[$userKey]['uincomingRpCount']);
            $usenderSum += strval($users[$userKey]['usenderRpCount']);
        }

        /* return [
            'users' => $users,
            'incomingSum' => $incomingSum,
            'senderSum' => $senderSum,
            'uincomingSum' => $uincomingSum,
            'usenderSum' => $usenderSum
        ]; */

        return view(
            'admin.document_mng.report.index',
            [
                'datas' => [
                    'users' => $users,
                    'incomingSum' => $incomingSum,
                    'senderSum' => $senderSum,
                    'uincomingSum' => $uincomingSum,
                    'usenderSum' => $usenderSum
                ]
            ]
        );
    }

    public function getReportCountOnDate(Request $request)
    {
        $date = $request->input('date');
        $date = strtotime($date);

        $incomingSum = 0;
        $senderSum = 0;
        $users = User::all()->toArray();

        foreach ($users as $userKey => $userVal) {
            /* Gelen başla */
            $inRp = DcReport::where([
                'user_id' => $userVal['id'],
                'rp_date' => $date,
                'rp_item_status' => "0"
            ])
            ->first();

            $users[$userKey]['incomingRpCount'] = $inRp ? $inRp['rp_count'] : 0;

            $incomingSum += strval($users[$userKey]['incomingRpCount']);
            /* Gelen bitiş */
            
            /* Gönderilen başla */
            $senRp = DcReport::where([
                'user_id' => $userVal['id'],
                'rp_date' => $date,
                'rp_item_status' => "1"
            ])
            ->first();

            $users[$userKey]['senderRpCount'] = $senRp ? $senRp['rp_count'] : 0;

            $senderSum += strval($users[$userKey]['senderRpCount']);
            /* Gönderilen bitiş */
        }

        $uincomingSum = 0;
        $usenderSum = 0;

        foreach ($users as $userKey => $userVal) {
            $users[$userKey]['uincomingRpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $date,
                'dc_item_status' => "0"
            ])
            ->count();

            $users[$userKey]['usenderRpCount'] = DcDocuments::where([
                'user_id' => $userVal['id'],
                'dc_date' => $date,
                'dc_item_status' => "1"
            ])
            ->count();

            $uincomingSum += strval($users[$userKey]['uincomingRpCount']);
            $usenderSum += strval($users[$userKey]['usenderRpCount']);
        }

        return [
            'users' => $users,
            'incomingSum' => $incomingSum,
            'senderSum' => $senderSum,
            'uincomingSum' => $uincomingSum,
            'usenderSum' => $usenderSum
        ];
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
        $incomingSum = 0;
        $senderSum = 0;

        foreach ($params['user_id'] as $paramKey => $paramVal) {
            $incomingDt = [
                'rp_date' => strtotime($params['rp_date']),
                'rp_count' => $params['incoming_rp_count'][$paramKey] ?? 0,
                'user_id' => $params['user_id'][$paramKey],
                'rp_item_status' => "0"
            ];
            
            $senderDt = [
                'rp_date' => strtotime($params['rp_date']),
                'rp_count' => $params['sender_rp_count'][$paramKey] ?? 0,
                'user_id' => $params['user_id'][$paramKey],
                'rp_item_status' => "1"
            ];

            $incomingSum += strval($incomingDt['rp_count']);
            $senderSum += strval($senderDt['rp_count']);

            /* Gelen evrak sayısı kayıt etme başla */
            $incomingRes = DcReport::where([
                'rp_date' => $incomingDt['rp_date'],
                'user_id' => $incomingDt['user_id'],
                'rp_item_status' => "0"
            ])->update(['rp_count' => $incomingDt['rp_count']]);

            if(!$incomingRes) {
                $incomingRes = DcReport::create($incomingDt);
            }
            /* Gelen evrak sayısı kayıt etme bitiş */

            /* Gönderilen evrak sayısı kayıt etme başla */
            $senderRes = DcReport::where([
                'rp_date' => $senderDt['rp_date'],
                'user_id' => $senderDt['user_id'],
                'rp_item_status' => "1"
            ])->update(['rp_count' => $senderDt['rp_count']]);

            if(!$senderRes) {
                $senderRes = DcReport::create($senderDt);
            }
            /* Gönderilen evrak sayısı kayıt etme bitiş */
        }

        return [
            'incomingSum' => $incomingSum,
            'senderSum' => $senderSum,
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

    public function getRecordNeedDocuments()
    {
        $users = User::with('dcReports')->get();

        foreach ($users as $userKey => $userVal) {
            foreach ($userVal->dcReports as $rpKey => $rpVal) {
                $date = $rpVal->rp_date;
                $rpVal->rp_date = date('d.m.Y', $rpVal->rp_date);
                
                $itemStatus = $rpVal->rp_item_status == 0 ? 'Gelen' : 'Giden' ;

                $count = DcDocuments::where([
                    ['dc_date', $date],
                    ['dc_item_status', $rpVal->rp_item_status],
                    ['user_id', $userVal->id]
                ])
                ->count();

                $count = $rpVal->rp_count - $count;

                if($count > 0) {
                    $dcReports[$rpVal->rp_date][$userVal->name][$itemStatus] = [
                        'rp_count' => $count
                    ];
                }
            }
        }

        return $dcReports ?? [];
    }
}
