<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use Illuminate\Http\Request;
use App\Models\Admin\DcLists;
use App\Models\Admin\DcDocuments;
use App\Http\Controllers\Controller;
use App\Http\Responsable\isAjaxResponse;
use App\Http\Requests\Admin\DocumentManagement\StoreDcListsRequest;
use App\Http\Requests\Admin\DocumentManagement\UpdateDcListsRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->toArray();

        $user = Auth::user();

        $datas = array_map(function($item) use ($user) {
            if($user->id === $item['id'] ) {
                $item['auth'] = true;
            }else {
                $item['auth'] = false;
            }
            return $item;
        }, $users);

        return view(
            'admin.document_mng.list.index',
            ['datas' => $datas]
        );
    }

    public function getListAndSelected(Request $request)
    {
        $params = $request->all();

        $dcLists = DcDocuments::find($params['dc_id'])->dc_lists();

        // dump($dcLists->pluck('dc_lists.id')->all());die;

        $list = $this->getList($dcLists->pluck('dc_lists.id')->all());

        return [
            'selected' => $dcLists->get(),
            'list' => $list
        ];
    }

    public function getList($ids = null)
	{
        if(empty($ids))
            $ids = [];
        else
            $ids = $ids;

        $list = DcLists::whereNotIn('id', $ids)->get();

        return $list;
    }

    public function getDataList(Request $request)
	{
	    $tblInfo = $request->all();

        $selectUserId = $tblInfo['user_id'];

        $notSelectCol = [
            'user_name',
            'user_id',
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

        $selectJoin = ", t1.name as user_name, t1.id as user_id";
        

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

        /* Editing only own objects */
        $userId = $request->user()->id;

        $datas = array_map(function($item) use ($userId) {
            if($item['user_id'] === $userId) {
                return $item;
            }else {
                $item['id'] = null;
                return $item;
            }
        }, $data->toArray());
        
	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $datas,
	        'draw' => $tblInfo['draw']
	    ];
	}

    public function addList(Request $request)
    {
        $params = $request->all();

        $dcDocument = DcDocuments::find($params['dc_id']);
        $dcList = DcLists::find($params['id']);

        $dcDocument->dc_lists()->save($dcList);

        return ['succeed' => __('messages.add_success')];
    }

    public function deleteList(Request $request)
    {
        $params = $request->all();

        // $dcDocument = DcDocuments::find($params['dc_id']);
        $dcList = DcLists::find($params['id']);
        $dcDocument = DcDocuments::find($params['dc_id']);

        $dcList->dc_documents()->detach($dcDocument);

        return ['succeed' => __('messages.add_success')];
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
     * @param  \App\Models\Admin\DcLists  $list
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
     * @param  \App\Models\Admin\DcLists  $list
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDcListsRequest $request, DcLists $list)
    {
        $params = $request->all();

        $list->fill($params)->save();
    
        return [
            'updatedItem' => $list,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcLists  $list
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
