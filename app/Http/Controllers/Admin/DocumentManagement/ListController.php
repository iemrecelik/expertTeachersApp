<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use Illuminate\Http\Request;
use App\Models\Admin\DcLists;
use App\Http\Controllers\Controller;
use App\Http\Responsable\isAjaxResponse;
use App\Http\Requests\Admin\DocumentManagement\StoreDcListsRequest;
use App\Http\Requests\Admin\DocumentManagement\UpdateDcListsRequest;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.document_mng.list.index');
    }

    public function getDataList(Request $request)
	{
	    $tblInfo = $request->all();

	    /*Array select and search columns*/
	    foreach ($tblInfo['columns'] as $column) {
	        
	        if (isset($column['data']))
	            $selectCol[] = $column['data'];

	        if($column['searchable'])
	            $searchCol[] = $column['data'];
	    }

	    /*Order settings*/
	    $colIndex = $tblInfo['order'][0]['column'];
	    $colOrder = $tblInfo['columns'][$colIndex]['data'];
	    $order = $tblInfo['order'][0]['dir'];


        /*join*/
        /* $join = [
            "dc_lists as t1", 
            "t0.dc_cat_id", '=', 
            "t1.id"
        ]; */
        $join = "";

        // $selectJoin = ", t1.dc_list_name as dc_up_cat_name";
        $selectJoin = "";
        

	    $dataList = DcLists::dataList([
	        'table' => 'dc_lists',
	        'fieldIDName' => 'id',
	        'addLangFields' => [],
            'choiceJoin' => 'leftJoin',
            /* 'join' => $join,
            'selectJoin' => $selectJoin, */
	        'selectCol' => $selectCol,
	        'searchCol' => $searchCol,
	        'colOrder' => $colOrder,
	        'order' => $order,
	        'search' => $tblInfo['search']['value'],
	    ]);

	    $recordsTotal = DcLists::count();
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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\StoreDcListsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDcListsRequest $request)
    {
        $params = $request->all();

        $params['user_id'] = $request->user()->id;

        DcLists::create($params);
    
        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\DcLists  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(DcLists $list)
    {
        return new isAjaxResponse($list);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateDcListsRequest  $request
     * @param  \App\Models\Admin\DcLists  $List
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDcListsRequest $request, DcLists $lists)
    {
        $params = $request->all();

        dump($params);
        dump($lists);die;

        $lists->fill($params)->save();
    
        return [
            'updatedItem' => $lists,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcLists  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DcLists $lists)
    {
        $res = $lists->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
