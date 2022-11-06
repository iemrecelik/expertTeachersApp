<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreTeachersRequest;
use App\Http\Requests\Admin\UpdateTeachersRequest;
use App\Http\Responsable\isAjaxResponse;
use App\Models\Admin\Teachers;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TeachersController extends Controller
{
    public function addExcel(Request $request)
    {
        $request->validate(
            [
                'excel_file' => 'required|file|mimes:xlsx,xls,xlx'
            ],
            [
                'excel_file.required' => 'Lütfen excel dosyası yükleyiniz.',
                'excel_file.file' => 'Lütfen sadece excel dosyası yükleyiniz.',
                'excel_file.mimes' => 'Lütfen sadece excel dosyası yükleyiniz.'
            ],
        );

        // Teachers::where('thr_gender', '0')->delete();die;

        $params = $request->all();

        /* Excel satır sayılarının eşitliğnin kontrolü başla */
        $rowArrLetter = [];
        $rowArrNumber = [];
        foreach ($params as $key => $val) {
            /* if(!in_array($key, ['excel_file', 'updateDb'])) {
                preg_match_all('/([0-9]+|[a-zA-Z]+)/', $val, $matches);

                $rowArrLetter[$key] = $matches[1][0];
                $rowArrNumber[] = $matches[1][1];
            } */
            if(!in_array($key, ['excel_file', 'updateDb'])) {
                $rowArr = explode('_', $val);
                
                if($key == 'thr_tc_no') {
                    $uniqueKey = $rowArr[0];
                }
                
                $rowArrLetter[$key] = $rowArr[0];
                $rowArrNumber[] = $rowArr[1];
            }
        }

        $rowArrNumber = array_unique($rowArrNumber);

        if(count($rowArrNumber) > 1) {
            throw ValidationException::withMessages(
                ['row' => 'Bütün satır sayıları aynı olmak zorundadır.']
            );
        }else if(count($rowArrNumber) < 1) {
            throw ValidationException::withMessages(
                ['row' => 'En az bir sütun seçmelisiniz.']
            );
        }
        /* Excel satır sayılarının eşitliğnin kontrolü bitiş */

        $inputFileType = 'Xlsx';
        $url = $params['excel_file']->getPathname();

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        
        $spreadsheet = $reader->load($url);

        $datas = $spreadsheet->getActiveSheet()->toArray();

        if ($params['updateDb']) {
            $tcnoArr = array_column($datas, 1);
            $existTeachQuery = Teachers::whereIn('thr_tc_no', $tcnoArr);

            $existTeachArr = $existTeachQuery->get()->toArray();
            $existTeachTcnoArr = array_column($existTeachArr, 'thr_tc_no');

            $existTeachQuery->delete();
        }else {
            $existTeachTcnoArr = [];
        }
        
        $co = 0;
        $index = 0;
        $insertArr = [];
        foreach ($datas as $key => $value) {
            $co++; 
            $index = $co % 50 == 0 ? $index + 1: $index;

            $exIndex = array_search($value[$uniqueKey], $existTeachTcnoArr);

            if($exIndex < 0) {
                foreach ($rowArrLetter as $letKey => $letVal) {
                    $arr[$letKey] = $value[$letVal];
                }
                $insertArr[$index][] = $arr;
                /* $insertArr[$index][] = [
                    'thr_gender' => '0',
                    'thr_tc_no' => $value[1],
                    'thr_name' => $value[2],
                    'thr_surname' => $value[2],
                    'thr_career_ladder' => $value[3],
                    'inst_id' => 1,
                ]; */
            }else {
                foreach ($rowArrLetter as $letKey => $letVal) {
                    $existTeachArr[$exIndex][$letKey] = $value[$letVal];
                }

                /* $existTeachArr[$exIndex]['thr_tc_no'] = $value[1];
                $existTeachArr[$exIndex]['thr_name'] = $value[2];
                $existTeachArr[$exIndex]['thr_surname'] = $value[2];
                $existTeachArr[$exIndex]['thr_career_ladder'] = $value[3]; */

                $insertArr[$index] = $existTeachArr[$exIndex];
            }
        }

        foreach ($insertArr as $insKey => $insVal) {
            Teachers::insert($insVal); 
        }

        return ['succeed' => __('messages.add_success')];
        
    }
    public function showTeacherInfos(Request $request)
    {
        $params = $request->all();
        $teacher = Teachers::where(
            'thr_tc_no', 
            $params['thr_tc_no']
        )
        ->with('dc_documents')
        ->with('institution')
        ->first();


        foreach ($teacher->dc_documents as $key => $dc_documents) {
            $dc_documents->dcFiles;
            $dc_documents->dcAttachFiles;

            $user = User::select('name as user_name')->find($dc_documents->user_id);
            $dc_documents->user_name = $user->user_name;
        }

        // dd($teacher);
/*         
        foreach ($teacher->dc_documents as $key => $dc_documents) {
            $dc_documents->dcFiles
        }
        
        $teacher->dc_documents[0]->dcFiles;
        $teacher->dc_documents[0]->dcAttachFiles; */

        return view(
            'admin.teachers.teacher_infos.teacher_infos',
            ['teacher' => $teacher]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.teachers.index');
    }

    public function getSearchTeacherList(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'searchTcNo' => 'required|integer'
            ],
            [
                'searchTcNo.required' => 'Tc no giriniz.',
                'searchTcNo.integer' => 'Sadece rakam giriniz.'
            ],
        );

        $params = $request->all();

        $teachers = Teachers::selectRaw('id, thr_tc_no, thr_name, thr_surname')
        ->whereRaw(
            'thr_tc_no LIKE :searchTcNo', 
            [
                'searchTcNo' => $params['searchTcNo'].'%'
            ]
        )
        ->get();

        $datas = array_map(function($teacher) {
            return [
                'id' => $teacher['id'],
                // 'label' => `({$teacher['thr_tc_no']}){$teacher['thr_name']} {$teacher['thr_surname']}`,
                'label' => "(T.C. No: ".$teacher['thr_tc_no'].")".$teacher['thr_name']." ".$teacher['thr_surname']
            ];
        }, $teachers->toArray());

        return $datas;
    }
        
    public function getDataList(Request $request)
	{
	    $tblInfo = $request->all();

        $notSelectCol = [
            'inst_name',
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
            "institutions as t1", 
            "t0.inst_id", '=', 
            "t1.id"
        ];

        $selectJoin = ", t1.inst_name";  

	    $dataList = Teachers::dataList([
	        'table' => 'teachers',
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

	    $recordsTotal = Teachers::count();
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
     * @param  \App\Http\Requests\Admin\StoreTeachersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeachersRequest $request)
    {
        $params = $request->all();

        $teacherExist = Teachers::where('thr_tc_no', $params['thr_tc_no']);

        if(!empty($teacherExist->first())) {
            throw ValidationException::withMessages(
                ['thr_tc_no' => "Aynı Tc'li veri ekleyemezsiniz."]
            );
        }

        $teacher = Teachers::create($params);

        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Teachers  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teachers $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\Teachers  $teacher
     * @return \Illuminate\Http\Response
     */
    public function edit(Teachers $teacher)
    {
        return new isAjaxResponse($teacher);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateTeachersRequest  $request
     * @param  \App\Models\Admin\Teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeachersRequest $request, Teachers $teacher)
    {
        $params = $request->all();

        $teacher->fill($params)->save();

        return [
            'updatedItem' => $teacher,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\Teachers $teachers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teachers $teacher)
    {
        $res = $teacher->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
