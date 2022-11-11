<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreTeachersRequest;
use App\Http\Requests\Admin\UpdateTeachersRequest;
use App\Http\Responsable\isAjaxResponse;
use App\Models\Admin\Teachers;
use App\Models\Admin\Institutions;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TeachersController extends Controller
{
    private $institutions = Array();
    private $institutionNames = Array();

    private function setInstitutionsInfos()
    {
        $this->institutions = Institutions::all()->toArray();
        $this->institutionNames = array_column($this->institutions, 'inst_name');

        $this->institutionNames = array_map(function($item) {
            return strtolower($item);
        }, $this->institutionNames);
    }

    public function addExcel(Request $request)
    {
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

        // dd($previewDatas);
        $sessionPreviewUniqueId = $previewDatas ? $previewDatas['previewUniqueId'] : null; 

        if($sessionPreviewUniqueId != $previewUniqueId) {
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
                $tcnoArr = array_column($datas, $uniqueKey);
                $existTeachQuery = Teachers::whereIn('thr_tc_no', $tcnoArr);

                $existTeachArr = $existTeachQuery->get()->toArray();
                $existTeachTcnoArr = array_column($existTeachArr, 'thr_tc_no');

                $existTeachQuery->delete();
            }else {
                $existTeachTcnoArr = [];
            }
            
            $co = 0;
            $min = min($rowArrNumber);

            $insertArr = [];
            $updateArr = [];
            foreach ($datas as $key => $value) {
                $co++;
                if(($min - 1) == $co) {
                    continue;
                }
                $arr = [];

                $exIndex = array_search($value[$uniqueKey], $existTeachTcnoArr);

                $this->setInstitutionsInfos();

                if(empty($exIndex)) {
                    foreach ($rowArrLetter as $letKey => $letVal) {
                        $val = $this->validateExcelField($letKey, $value[$letVal]);

                        if($val === null) {
                            $arr = null;
                            break;
                        }
                        $arr[$letKey] = $val;
                    }

                    if(!empty($arr)) {
                        $insertArr[] = $arr;
                    }

                }else {
                    foreach ($rowArrLetter as $letKey => $letVal) {
                        $val = $this->validateExcelField($letKey, $value[$letVal]);
                        
                        if($val === null) {
                            $existTeachArr[$exIndex] = null;
                            break;
                        }
                        $existTeachArr[$exIndex][$letKey] = $val;
                    }

                    if(!empty($existTeachArr[$exIndex])) {
                        $updateArr[] = $existTeachArr[$exIndex];
                    }
                }
            }
        }else {
            $insertArr = $previewDatas['insertArr'];
            $updateArr = $previewDatas['updateArr'];
        }

        if($preview !== 'true') {
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
            $datas = [
                'insertArr' => $insertArr,
                'updateArr' => $updateArr,
                'institutions' => $this->institutions,
                'previewUniqueId' => $previewUniqueId,
            ];
    
            $request->session()->put('previewDatas', $datas);
        }

        return [
            'succeed' => __('messages.add_success')
        ];
    }

    public function preview(Request $request)
    {
        $datas = $request->session()->get('previewDatas');
        
        $css = '
            table {
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align: left;
            }
            th, td {
                border: 1px solid #000;
                margin: 0px;
            }
        ';

        $title = '
            <thead>
                <tr>
                    <td>TC NO</td>
                    <td>AD</td>
                    <td>SOYAD</td>
                    <td>İL</td>
                    <td>İLÇE</td>
                    <td>MAİL</td>
                    <td>TEL NO</td>
                    <td>CİNSİYET</td>
                    <td>KARİYER DURUMU</td>
                    <td>ÜNVANI</td>
                    <td>GÖREVİ</td>
                    <td>GÖREV YERİ</td>
                    <td>EĞİTİM DURUMU</td>
                    <td>KURUMU</td>
                </tr>
            </thead>
        ';

        $tblContentInsertArr = $this->createHtmlTable($datas['insertArr'], $datas['institutions']);
        $tblContentUpdateArr = $this->createHtmlTable($datas['updateArr'], $datas['institutions']);

        $html = '
            <div class="table-wrapper">
                <table class="fl-table table table-striped table-inverse table-responsive table-bordered">
                    '.$title.'
                    '.$tblContentInsertArr.'
                    '.$tblContentUpdateArr.'
                </table>
            </div>
        ';

        return $html;

        $mpdf = New \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir')]);
        
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($css,1);
        $mpdf->WriteHTML($html,2);

        $mpdf->Output();

        return view(
            'admin.teachers.preview',
            ['datas' => $request->session()->get('previewDatas')]
        );
    }

    private function createHtmlTable($datas, $institutions)
    {
        $careerLadderArr = ['Bilinmiyor', 'Öğretmen', 'Uzman Öğretmen', 'Başöğretmen'];
        $tblContent = '';
        
        foreach ($datas as $updKey => $updVal) {
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

    private function validateExcelField($name, $value)
    {
        $val = null;
        switch ($name) {
            case 'thr_tc_no':
                if(strlen($value) == 11) {
                    $val = $value;
                }
                break;
            case 'thr_gender':
                $val = array_search(mb_strtolower($value), ['erkek', 'bayan']);
                $val = $val !== false ? strval($val) : null;
                break;
            case 'thr_career_ladder':
                $val = array_search(mb_strtolower($value), ['bilinmiyor', 'öğretmen', 'uzman öğretmen', 'başöğretmen']);
                $val = ($val - 1);
                $val = $val !== false ? strval($val) : null;
                break;
            case 'inst_id':
                $val = array_search(mb_strtolower($value), $this->institutionNames);
                $val = $val !== false ? $this->institutions[$val]['id'] : null;
                break;
            case 'thr_birth_day':
                if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])[.|\/](0[1-9]|1[0-2])[.|\/][0-9]{4}$/", $value)) {
                    $val = strtotime($value);
                }
                break;
            default:
                if(in_array($name, [
                    'thr_name', 'thr_surname', 'thr_province', 
                    'thr_town', 'thr_email', 'thr_degree', 
                    'thr_task', 'thr_education', 'thr_mobile_no', 
                    'thr_place_of_task', 'thr_birth_day'
                ])) {
                    $val = $value;
                }
                break;
        }

        return $val;
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
