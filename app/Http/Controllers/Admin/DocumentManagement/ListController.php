<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            'join' => $join,
            'selectJoin' => $selectJoin,
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
     * @param  \App\Http\Requests\Admin\DocumentManagement\StoreDcListRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDcListRequest $request)
    {
        $params = $request->all();

        $childList = DcList::create($params);

        if($params['dc_cat_id'] > 0) {

            $dcList = DcList::find($params['dc_cat_id']);
            
            $dcList->childList()->save($childList);
        }
    
        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateDcListRequest  $request
     * @param  \App\Models\Admin\DcList  $List
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDcListRequest $request, DcList $List)
    {
        $params = $request->all();

        $list->fill($params)->save();

        if($params['dc_cat_id'] > 0) {

            $dcList = DcList::find($params['dc_cat_id']);
            
            $dcList->childList()->save($List);
        }
        
        /* $book->updateMany([
            'childDatas' => $params['langs'],
            'childName' => 'booksLang',
            'childInstance' => new BooksLang(),
        ]); */
    
        return [
            'updatedItem' => $list,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcLists  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DcLists $list)
    {
        $res = $list->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
