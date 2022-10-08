<?php

namespace App\Http\Controllers\Admin\LawsuitManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\LawsuitsManagement\StoreLawsuitsRequest;
use App\Http\Requests\Admin\LawsuitsManagement\UpdateLawsuitsRequest;
use App\Http\Responsable\isAjaxResponse;
use App\Models\Admin\Lawsuits;
use Illuminate\Validation\ValidationException;

class LawsuitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.lawsuits_mng.lawsuits.index');
    }

    public function getLawsuits(Request $request)
	{
        $req = $request->all();

        if(empty($req['id'])) {
            $lawsuitList = Lawsuits::all();
        } else {
            $lawsuitList = Lawsuits::find($req['id']);
        }
        
        return $lawsuitList;
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

	    $dataList = Lawsuits::dataList([
	        'table' => 'lawsuits',
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

	    $recordsTotal = Lawsuits::count();
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
     * @param  \App\Http\Requests\Admin\LawsuitsManagement\StoreLawsuitsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLawsuitsRequest $request)
    {
        $params = $request->all();

        $lawsuitExist = Lawsuits::where('uns_name', $params['uns_name']);

        if(!empty($lawsuitExist->first())) {
            throw ValidationException::withMessages(
                ['uns_name' => "Zaten sendika yüklü lütfen başka sendika ismi yazınız."]
            );
        }

        $lawsuit = Lawsuits::create($params);

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
