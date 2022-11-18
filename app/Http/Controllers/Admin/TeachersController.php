<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Library\FileUpload;
use App\Library\ExcelProcess;
use App\Models\Admin\Teachers;
use App\Models\Admin\Institutions;
use App\Http\Controllers\Controller;
use App\Http\Responsable\isAjaxResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\StoreTeachersRequest;
use App\Http\Requests\Admin\UpdateTeachersRequest;

class TeachersController extends Controller
{
    public function addExcel(Request $request)
    {
        // Teachers::whereNotIn('id', ['1'])->delete();
        /* $request->validate(
            [
                'excel_file' => 'required|file|mimes:xlsx,xls,xlx',
                'thr_tc_no' => 'required',
                'thr_name' => 'required',
                'thr_surname' => 'required',
                'thr_career_ladder' => 'required',
                'inst_id' => 'required',
                'thr_gender' => 'required',
            ],
            [
                'excel_file.required' => 'Lütfen excel dosyası yükleyiniz.',
                'excel_file.file' => 'Lütfen sadece excel dosyası yükleyiniz.',
                'excel_file.mimes' => 'Lütfen sadece excel dosyası yükleyiniz.',
                'thr_tc_no.required' => 'Tc alanı zorunludur.',
                'thr_name.required' => 'İsim alanı zorunludur.',
                'thr_surname.required' => 'Soy isim alanı zorunludur.',
                'thr_career_ladder.required' => 'Kariyer basamağı alanı zorunludur.',
                'inst_id.required' => 'Kurum alanı zorunludur.',
                'thr_gender.required' => 'Cinsiyet alanı zorunludur.',
            ],
        ); */

        $params = $request->all();

        $preview = $params['preview'];
        unset($params['preview']);

        $previewUniqueId = $params['previewUniqueId'];
        unset($params['previewUniqueId']);
        
        $params['thr_tc_no'] = '0_2';
        $params['thr_name'] = '1_2';
        $params['thr_surname'] = '2_2';
        $params['thr_career_ladder'] = '14_2';
        $params['inst_id'] = '12_2';
        $params['thr_gender'] = '13_2';

        $previewDatas = $request->session()->get('previewDatas');

        $sessionPreviewUniqueId = $previewDatas ? $previewDatas['previewUniqueId'] : null; 
        
        if($sessionPreviewUniqueId != $previewUniqueId) {
            $request->session()->forget('previewDatas');
            $insertArr = [];
            $updateArr = [];
            $insertErrorArr = [];

            $excelProcess = new ExcelProcess();
            $excelDatas = $excelProcess->getExcelDatas($params, 'thr_tc_no', 'Teachers');

            $insertArr = $excelDatas['insertArr'];
            $updateArr = $excelDatas['updateArr'];
            $insertErrorArr = $excelDatas['insertErrorArr'];
        }else {
            $insertArr = $previewDatas['insertArr'];
            $updateArr = $previewDatas['updateArr'];
            $insertErrorArr = $previewDatas['insertErrorArr'];
        }

        if($preview !== 'true') {
            $sumInsertData = count($insertArr) + count($updateArr);
            if(count($insertArr) > 0) {
                $insertArr = array_chunk($insertArr, 50);
    
                foreach ($insertArr as $insKey => $insVal) {
                    Teachers::insert($insVal); 
                }
            }
    
            if(count($updateArr) > 0) {
                $updateArr = array_chunk($updateArr, 50);
    
                foreach ($updateArr as $updKey => $updVal) {
                    Teachers::insert($updVal);
                }
            }
        }else {
            $institutions = Institutions::all()->toArray();

            $tbodyInsertHtml = $this->createHtmlTable($insertArr, $institutions, 10);
            $tbodyUpdateHtml = $this->createHtmlTable($updateArr, $institutions, 10);

            $request->session()->put('previewDatas', [
                'insertArr' => $insertArr,
                'updateArr' => $updateArr,
                'insertErrorArr' => $insertErrorArr,
                'previewUniqueId' => $previewUniqueId,
            ]);

            return view(
                'admin.teachers.preview',
                [
                    'datas' => [
                        'tbodyHtml' => $tbodyInsertHtml.$tbodyUpdateHtml,
                        'insertArr' => $insertArr,
                        'updateArr' => $updateArr,
                        'insertErrorArr' => $insertErrorArr,
                    ]
                ]
            );
        }

        // return ['succeed' => __('messages.add_success')];

        /* return [
            'datas' => [
                'insertErrorArr' => $insertErrorArr,
                'sumErrorData' => count($insertErrorArr),
                'sumInsertData' => $sumInsertData,
                'succeed' => __('messages.add_success')
            ]
        ]; */

        return redirect()->route('admin.teachers.index')->with('datas', 
            [
                'insertErrorArr' => $insertErrorArr,
                'sumErrorData' => count($insertErrorArr),
                'sumInsertData' => $sumInsertData,
                'succeed' => __('messages.add_success')
            ]
        );
    }

    public function storeImages(Request $request)
    {
        $request->validate(
            [
                'images_file.*' => 'required|file|image|max:2048'
            ],
            [
                'images_file.*.required' => 'Lütfen resim dosyası giriniz.',
                'images_file.*.image' => 'Sadece resim dosyası giriniz.',
                'images_file.*.file' => 'Lütfen resim dosyası giriniz.',
                'images_file.*.max' => 'Lütfen 2gb dan daha küçük resim dosyası yükleyiniz.',
            ],
        );

        $params = $request->all();

        $fileUpload = new FileUpload();

        foreach ($params['images_file'] as $key => $val) {
            $tcno[] = pathinfo($val->getClientOriginalName(), PATHINFO_FILENAME);

            var_dump($tcno);

            /* $fileUpload->setConfig($val, null, 'JPG');
            $fileUpload->saveFile(); */
        }

        die;

        
    }

    public function storeExcel(Request $request)
    {
        $previewDatas = $request->session()->get('previewDatas');
        $insertArr = $previewDatas['insertArr'];
        $updateArr = $previewDatas['updateArr'];
        $insertErrorArr = $previewDatas['insertErrorArr'];
        $sumInsertData = count($insertArr) + count($updateArr);

        if(count($insertArr) > 0) {
            $insertArr = array_chunk($insertArr, 50);

            foreach ($insertArr as $insKey => $insVal) {
                Teachers::insert($insVal); 
            }
        }

        if(count($updateArr) > 0) {
            $updateArr = array_chunk($updateArr, 50);

            foreach ($updateArr as $updKey => $updVal) {
                Teachers::insert($updVal);
            }
        }

        $request->session()->forget('previewDatas');

        return redirect()->route('admin.teachers.index')->with('datas', 
            [
                'insertErrorArr' => $insertErrorArr,
                'sumErrorData' => count($insertErrorArr),
                'sumInsertData' => $sumInsertData,
                'succeed' => __('messages.add_success')
            ]
        );
    }

    private function createHtmlTable($datas, $institutions, $limit = 50)
    {
        $careerLadderArr = ['Bilinmiyor', 'Öğretmen', 'Uzman Öğretmen', 'Başöğretmen'];
        $tblContent = '';
        $co = 0;
        
        foreach ($datas as $updKey => $updVal) {
            $co++;
            if($co === $limit){
                break;
            }
            $instIndex = array_search($updVal['inst_id'], array_column($institutions, 'id'));
            $careerLadderIndex = strval($updVal['thr_career_ladder']) + 1;

            $tcNo       = $updVal['thr_tc_no'];
            $name       = $updVal['thr_name'];
            $surname    = $updVal['thr_surname'];
            $province   = empty($updVal['thr_province']) ? '': $updVal['thr_province'];
            $town       = empty($updVal['thr_town']) ? '': $updVal['thr_town'];
            $email      = empty($updVal['thr_email']) ? '': $updVal['thr_email'];
            $mobileNo   = empty($updVal['thr_mobile_no']) ? '': $updVal['thr_mobile_no'];
            $gender     = $updVal['thr_gender'] == 0 ? 'Erkek' : 'Bayan';
            $careerLadder = $careerLadderArr[$careerLadderIndex];
            $degree     = empty($updVal['thr_degree']) ? '': $updVal['thr_degree'];
            $task       = empty($updVal['thr_task']) ? '': $updVal['thr_task'];
            $placeOfTask = empty($updVal['thr_place_of_task']) ? '': $updVal['thr_place_of_task'];
            $educationSt = empty($updVal['thr_education_st']) ? '': $updVal['thr_education_st'];
            $instId     = $institutions[$instIndex]['inst_name'];

            $tblContent .= '
                <tr>
                    <td>'.$tcNo.'</td>
                    <td>'.$name.'</td>
                    <td>'.$surname.'</td>
                    <td>'.$province.'</td>
                    <td>'.$town.'</td>
                    <td>'.$email.'</td>
                    <td>'.$mobileNo.'</td>
                    <td>'.$gender.'</td>
                    <td>'.$careerLadder.'</td>
                    <td>'.$degree.'</td>
                    <td>'.$task.'</td>
                    <td>'.$placeOfTask.'</td>
                    <td>'.$educationSt.'</td>
                    <td>'.$instId.'</td>
                </tr>
            ';
        }

        return $tblContent;
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
    public function index(Request $request)
    {
        return view(
            'admin.teachers.index', 
            ['datas' => [
                'succeed' => [],
                'insertErrorArr' => [],
            ]]
        );
    }

    public function getSearchTeacherList(Request $request)
    {
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
