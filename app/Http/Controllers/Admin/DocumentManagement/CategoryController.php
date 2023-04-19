<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\DocumentManagement\StoreDcCategoryRequest;
use App\Http\Requests\Admin\DocumentManagement\UpdateDcCategoryRequest;
use App\Http\Responsable\isAjaxResponse;
use App\Models\Admin\DcCategory;
use Illuminate\Validation\ValidationException;
use App\Library\LogInfo;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class CategoryController extends Controller
{
    public function __construct() {
        $this->middleware(['permission:show module categories'])->only('index');
        $this->middleware(['permission:create categories'])->only('store');
        $this->middleware(['permission:edit categories'])->only('edit');
        $this->middleware(['permission:edit categories'])->only('update');
        $this->middleware(['permission:delete categories'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.document_mng.category.index');
    }

    public function getCategory(Request $request)
	{
        $req = $request->all();

        if(empty($req['id']))
            $id = null;
        else
            $id = $req['id'];
            
        
        $categoryList = $this->getTreeviewCat(null, $id);
        
        if(isset($req['startData'])) {
            array_unshift($categoryList, $req['startData']);
        }/* else {
            array_unshift($categoryList, [
                'id' => 0,
                'label' => 'Üst Kategori Yok'
            ]);
        } */

        return $categoryList;
    }

    private function getTreeviewCat($id = null, $whereNotId = null) 
    {
        $arr = [];
        $co = 0;
        
        $cats = DcCategory::where('dc_cat_id', $id)
                ->where('id', '!=', $whereNotId)
                ->orderBy('dc_cat_name')
                ->get();

        if (!$cats->isEmpty()) {
            foreach ($cats as $key => $val) {
                $arr[$co]['id'] = $val->id;
                $arr[$co]['label'] = $val->dc_cat_name;

                $child = $this->getTreeviewCat($val->id, $whereNotId);

                if ($child) {
                    $arr[$co]['children'] = $this->getTreeviewCat($val->id, $whereNotId);
                }

                $co++;
            }
            return $arr;
        } else {
            return null;
        }
        
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
        $join = [
            "dc_category as t1", 
            "t0.dc_cat_id", '=', 
            "t1.id"
        ];

        $selectJoin = ", t1.dc_cat_name as dc_up_cat_name";

	    $dataList = DcCategory::dataList([
	        'table' => 'dc_category',
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

	    $recordsTotal = DcCategory::count();
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
     * @param  \App\Http\Requests\Admin\DocumentManagement\StoreDcCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDcCategoryRequest $request)
    {
        $params = $request->all();

        $dcCat = DcCategory::where('dc_cat_name', $params['dc_cat_name']);

        if(!empty($dcCat->first())) {
            throw ValidationException::withMessages(
                ['dc_cat_name' => 'Aynı isimde konu ekleyemezsiniz.']
            );
        }

        $childCategory = DcCategory::create($params);

        if($params['dc_cat_id'] > 0) {

            $dcCategory = DcCategory::find($params['dc_cat_id']);
            
            $dcCategory->childCategory()->save($childCategory);
        }

        $logInfo = new LogInfo('Kategori Modülü');
        $logInfo->crCreateLog($childCategory);
    
        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(DcCategory $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\DcCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(DcCategory $category)
    {
        /* $book = DcCategory::with('booksLang')
        ->where('books.id', $id)
        ->first(); */

        return new isAjaxResponse($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateDcCategoryRequest  $request
     * @param  \App\Models\Admin\DcCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDcCategoryRequest $request, DcCategory $category)
    {
        $params = $request->all();
        $oldCat = clone $category;

        $category->fill($params)->save();

        if($params['dc_cat_id'] > 0) {

            $dcCategory = DcCategory::find($params['dc_cat_id']);
            
            $dcCategory->childCategory()->save($category);
        }

        $logInfo = new LogInfo('Kategori Modülü');
        $logInfo->crUpdateLog($oldCat, $category);
    
        return [
            'updatedItem' => $category,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DcCategory $category)
    {
        $oldCat = $category;
        $res = $category->delete();
        
        $logInfo = new LogInfo('Kategori Modülü');
        $logInfo->crDestroyLog($oldCat);

        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }

    public function updateOrder(Request $request)
    {
        $request->all();
        $request->validate(
            [
                'id' => 'required|integer',
                'dc_order' => 'required|integer',
            ],
            [
                'dc_order.required' => 'Sıra numarası boş olamaz.',
                'dc_order.integer' => 'Sıra numarası rakam olmalıdır.',
            ]
        );

        $orderNumber = $request->input('dc_order');
        $id = $request->input('id');

        $category = DcCategory::where('id', $id)
            ->update(['dc_order' => $orderNumber]);

        return [
            'updatedItem' => $category,
            'succeed' => __('messages.edit_success')
        ];
    }
}
