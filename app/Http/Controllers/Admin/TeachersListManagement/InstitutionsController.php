<?php

namespace App\Http\Controllers\Admin\TeachersListManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TeachersListManagement\StoreInstitutionsRequest;
use App\Http\Requests\Admin\TeachersListManagement\UpdateInstitutionsRequest;
use App\Http\Responsable\isAjaxResponse;
use App\Models\Admin\Institutions;
use Illuminate\Validation\ValidationException;

class InstitutionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.institutions.index');
    }

    public function getInstitutions(Request $request)
	{
        $req = $request->all();

        if(empty($req['id'])) {
            $institutionsList = Institutions::all();
        } else {
            $institutionsList = Institutions::find($req['id']);
        }
        
        /* $institutionsList = $this->getTreeviewCat(null, $id);
        
        if(isset($req['startData'])) {
            array_unshift($institutionsList, $req['startData']);
        }else {
            array_unshift($institutionsList, [
                'id' => 0,
                'label' => 'Üst Kategori Yok'
            ]);
        } */

        return $institutionsList;
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

	    $dataList = Institutions::dataList([
	        'table' => 'institutions',
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

	    $recordsTotal = Institutions::count();
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
     * @param  \App\Http\Requests\Admin\StoreInstitutionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInstitutionsRequest $request)
    {
        $params = $request->all();

        $institutionExist = Institutions::where('inst_name', $params['inst_name']);

        if(!empty($institutionExist->first())) {
            throw ValidationException::withMessages(
                ['inst_name' => "Zaten kurum yüklü lütfen başka kurum ismi yazınız."]
            );
        }

        $institution = Institutions::create($params);

        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Institutions  $institution
     * @return \Illuminate\Http\Response
     */
    public function show(Institutions $institution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Institutions  $institution
     * @return \Illuminate\Http\Response
     */
    public function edit(Institutions $institution)
    {
        return new isAjaxResponse($institution);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateInstitutionsRequest  $request
     * @param  \App\Models\Admin\Institutions  $institution
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInstitutionsRequest $request, Institutions $institution)
    {
        $params = $request->all();

        $institution->fill($params)->save();

        return [
            'updatedItem' => $institution,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Institutions $institution
     * @return \Illuminate\Http\Response
     */
    public function destroy(Institutions $institution)
    {
        $res = $institution->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
