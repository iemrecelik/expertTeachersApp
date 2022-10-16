<?php

namespace App\Http\Controllers\Admin\LawsuitManagement;

use Illuminate\Http\Request;
use App\Models\Admin\Lawsuits;
use App\Models\Admin\Subjects;
use App\Models\Admin\DcDocuments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Responsable\isAjaxResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\LawsuitsManagement\StoreLawsuitsRequest;
use App\Http\Requests\Admin\LawsuitsManagement\UpdateLawsuitsRequest;

class LawsuitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $inputFileType = 'Xlsx';

        // $url = Storage::url('belge.xlsx');
        $url = storage_path('app/public/belge3.xlsx');

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        // $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($url);


        $datas = $spreadsheet->getActiveSheet()->toArray();
        echo '<pre>';
        foreach ($datas as $key => $value) {

            var_dump($value[0]);
            var_dump($value[1]);
            var_dump($value[2]);
            var_dump($value[3]);
            var_dump($value[4]);
            var_dump($value[5]);
            var_dump($value[6]);
        }
        echo '</pre>';
        die; */
        
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

        $notSelectCol = [
            'thr_name',
            'dc_date',
        ];

	    /*Array select and search columns*/
	    foreach ($tblInfo['columns'] as $column) {
	        
	        if (isset($column['data']))
                if(!in_array($column['data'], $notSelectCol))
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
            "dc_documents as t1", 
            "t0.dc_id", '=', 
            "t1.id"
        ];

        $selectJoin = ", t1.dc_number, t1.dc_date";  

	    $dataList = Lawsuits::dataList([
	        'table' => 'lawsuits',
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

        $dataList->leftJoin('teachers as t2', 't2.id', '=', 't0.thr_id');
        $dataList->selectRaw('t2.thr_name');
        
        $dataList->leftJoin('unions as t3', 't3.id', '=', 't0.uns_id');
        $dataList->selectRaw('t3.uns_name');

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

        $dcDownIds = $params['dc_down_id'] ?? [];
        
        if(!empty($params['sub_order'])) {
            $subOrders = $params['sub_order'];
            unset($params['sub_order']);

            $subDescriptions = $params['sub_description'];
            unset($params['sub_description']);
        }

        $lawsuit = Lawsuits::create($params);
        
        /* add down dc_documents start */
        if(count($dcDownIds) > 0) {
            unset($params['dc_down_id']);

            // $key = array_search($params['dc_id'], $dcDownIds);

            $dcDownIds = array_map(function($item) use ($params) {
                if($params['dc_id'] != $item) {
                    return $item;
                }
            }, $dcDownIds);

            $dcDownIds = array_unique($dcDownIds);

            /* if(!empty($key)) {
                unset($dcDownIds[$key]);
            } */

            $downDcDocuments = DcDocuments::whereIn('id', $dcDownIds)->get();

            $lawsuit->dc_documents()->saveMany($downDcDocuments);
        }
        /* add down dc_documents end */

        /* add subjects start */
        for ($i=0; $i < count($subOrders); $i++) {
            if(empty($subDescriptions[$i])) {
                continue;
            }
            $subObj = new Subjects();
            $subObj->sub_order = $subOrders[$i];
            $subObj->sub_description = $subDescriptions[$i];
            $subObj->law_id = $lawsuit->id;
            $subjectArr[$i] = $subObj;
        }

        if(!empty($subjectArr)) {
            $lawsuit->subjects()->saveMany($subjectArr);
        }
        /* add subjects end */

        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Lawsuits  $lawsuit
     * @return \Illuminate\Http\Response
     */
    public function show(Lawsuits $lawsuit)
    {
        //
    }

    private static function cmp($a, $b)
    {
        if ($a->sub_order == $b->sub_order) {
            return 0;
        }
        return ($a->sub_order < $b->sub_order) ? -1 : 1;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Lawsuits  $lawsuit
     * @return \Illuminate\Http\Response
     */
    public function edit(Lawsuits $lawsuit)
    {
        $lawsuit->dc_document;
        $lawsuit->dc_documents;
        $subjects = $lawsuit->subjects->toArray();

        if (!empty($subjects)) {
            usort($subjects, function ($a, $b) {
                if ($a['sub_order'] == $b['sub_order']) {
                    return 0;
                }
                return ($a['sub_order'] < $b['sub_order']) ? -1 : 1;
            });

            $lawsuit->subjectsSort = $subjects;
        }

        $lawsuit->teacher;
        $lawsuit->union;

        $lawsuit->mainDcDocument = [
            'date' => date("d/m/Y", $lawsuit->dc_document->dc_date),
            'itemStatus' => $lawsuit->dc_document->dc_item_status == 0 
                ? 'Gelen Evrak'
                : 'Giden Evrak'
        ];

        if(!empty($lawsuit->dc_documents)) {
            $lawsuit->relDcDocuments = array_map(function($item) {
                return [
                    'date' => date("d/m/Y", $item['dc_date']),
                    'itemStatus' => $item['dc_item_status'] == 0 
                        ? 'Gelen Evrak'
                        : 'Giden Evrak'
                ];
            }, $lawsuit->dc_documents->toArray());
        }

        return new isAjaxResponse($lawsuit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateLawsuitsRequest  $request
     * @param  \App\Models\Admin\Lawsuits  $lawsuit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLawsuitsRequest $request, Lawsuits $lawsuit)
    {
        $params = $request->all();

        $dcDownIds = $params['dc_down_id'] ?? [];
        
        if(!empty($params['sub_order'])) {
            $subOrders = $params['sub_order'];
            unset($params['sub_order']);

            $subDescriptions = $params['sub_description'];
            unset($params['sub_description']);
        }else {
            $subOrders = [];
        }

        DB::table('law_dc')->where('law_id', '=', $lawsuit->id)->delete();

        /* add down dc_documents start */
        if(count($dcDownIds) > 0) {
            unset($params['dc_down_id']);

            $dcDownIds = array_map(function($item) use ($params) {
                if($params['dc_id'] != $item) {
                    return $item;
                }
            }, $dcDownIds);

            $dcDownIds = array_unique($dcDownIds);

            $downDcDocuments = DcDocuments::whereIn('id', $dcDownIds)->get();

            $lawsuit->dc_documents()->saveMany($downDcDocuments);
        }
        /* add down dc_documents end */

        /* add subjects start */
        $lawsuit->subjects()->delete();

        for ($i=0; $i < count($subOrders); $i++) {
            if(empty($subDescriptions[$i])) {
                continue;
            }
            $subObj = new Subjects();
            $subObj->sub_order = $subOrders[$i];
            $subObj->sub_description = $subDescriptions[$i];
            $subObj->law_id = $lawsuit->id;
            $subjectArr[$i] = $subObj;
        }

        if(!empty($subjectArr)) {
            $lawsuit->subjects()->saveMany($subjectArr);
        }
        /* add subjects end */

        $params['thr_id'] = $params['thr_id'] ?? null;
        $params['uns_id'] = $params['uns_id'] ?? null;

        $lawsuit->fill($params)->save();

        return [
            'updatedItem' => $lawsuit,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Lawsuits $lawsuit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lawsuits $lawsuit)
    {
        $res = $lawsuit->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
