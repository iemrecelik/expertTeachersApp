<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreUnionsRequest;
use App\Http\Requests\Admin\UpdateUnionsRequest;
use App\Http\Responsable\isAjaxResponse;
use App\Models\Admin\Unions;
use Illuminate\Validation\ValidationException;

class UnionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.unions.index');
    }

    public function getUnions(Request $request)
	{
        $req = $request->all();

        if(empty($req['id'])) {
            $unionList = Unions::all();
        } else {
            $unionList = Unions::find($req['id']);
        }
        
        return $unionList;
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
            "dc_category as t1", 
            "t0.dc_cat_id", '=', 
            "t1.id"
        ];

        $selectJoin = ", t1.dc_cat_name as dc_up_cat_name"; */  

	    $dataList = Unions::dataList([
	        'table' => 'unions',
	        'fieldIDName' => 'id',
	        'addLangFields' => [],
            /* 'choiceJoin' => 'leftJoin',
            'join' => $join,
            'selectJoin' => $selectJoin, */
	        'selectCol' => $selectCol,
	        'searchCol' => $searchCol,
	        'colOrder' => $colOrder,
	        'order' => $order,
	        'search' => $tblInfo['search']['value'],
	    ]);

	    $recordsTotal = Unions::count();
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUnionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUnionsRequest $request)
    {
        $params = $request->all();

        $institutionExist = Unions::where('uns_name', $params['uns_name']);

        if(!empty($institutionExist->first())) {
            throw ValidationException::withMessages(
                ['uns_name' => "Zaten sendika yüklü lütfen başka sendika ismi yazınız."]
            );
        }

        $institution = Unions::create($params);

        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Unions  $institution
     * @return \Illuminate\Http\Response
     */
    public function show(Unions $union)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Unions  $union
     * @return \Illuminate\Http\Response
     */
    public function edit(Unions $union)
    {
        return new isAjaxResponse($union);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateUnionsRequest  $request
     * @param  \App\Models\Admin\Unions  $union
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUnionsRequest $request, Unions $union)
    {
        $params = $request->all();

        $union->fill($params)->save();

        return [
            'updatedItem' => $union,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Unions $union
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unions $union)
    {
        $res = $union->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
