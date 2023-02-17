<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Library\FileUpload;
use Illuminate\Http\Request;
use App\Library\ExcelProcess;
use App\Models\Admin\Teachers;
use App\Models\Admin\DcDocuments;
use App\Models\Admin\Institutions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Responsable\isAjaxResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\StoreTeachersRequest;
use App\Http\Requests\Admin\UpdateTeachersRequest;
use App\Models\Admin\LawsuitFiles;
use App\Rules\ValidateTCNo;
use Illuminate\Support\Facades\DB;
use App\Library\LogInfo;


class TeachersController extends Controller
{
    private $provinces = Array();
    private $towns = Array();

    public function __construct()
    {
        $provincesTbl = DB::table('provinces')->get();
        foreach ($provincesTbl as $prvKey => $prvVal) {
            $this->provinces[
                \Transliterator::create('tr-title')->transliterate($prvVal->prv_name)
            ] = \Transliterator::create('tr-title')->transliterate($prvVal->id);
        }

        $townsTbl = DB::table('towns')->get();
        foreach ($townsTbl as $twnKey => $twnVal) {
            $this->towns[
                \Transliterator::create('tr-title')->transliterate($twnVal->twn_name)
                .'_'.$twnVal->prv_id
            ] = \Transliterator::create('tr-title')->transliterate($twnVal->id);
        }    
    }

    public function addDocumentToTeacher(Request $request)
    {
        $id = $request->input('id');
        $dcId = $request->input('dc_id');
        
        $existDc = DB::table('dc_thr')->where([
            ['dc_id', $dcId],
            ['thr_id', $id],
        ])->get();

        if(count($existDc) > 0) {
            $result = null;
        }else {
            $teacher = Teachers::find($id);
            $document = DcDocuments::find($dcId);

            $teacher->dc_documents()->save($document);
            
            $document->dcFiles;

            $result = $document;
        }

        $logInfo = new LogInfo('Öğretmen Modülü');
        $logInfo->crShowLog(
            "Ekleme::Öğretmene İlişkilendirilen Yazı::{$teacher->thr_tc_no} {$teacher->thr_name} {$teacher->thr_surname} adlı öğretmene {$document->dc_number} sayılı yazı eklendi."
        );

        return $result;
    }
    public function delDocumentToTeacher(Teachers $teacher, DcDocuments $document)
    {
        $res = DB::table('dc_thr')->where([
            ['dc_id', $document->id],
            ['thr_id', $teacher->id]
        ])->delete();

        $logInfo = new LogInfo('Öğretmen Modülü');
        $logInfo->crShowLog(
            "Silme::Öğretmene İlişkilendirilen Yazı::{$teacher->thr_tc_no} {$teacher->thr_name} {$teacher->thr_surname} adlı öğretmenden {$document->dc_number} sayılı yazı silindi."
        );

        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }

    public function addExcelValidation(Request $request)
    {
        $request->validate(
            [
                'excel_file' => 'required|file|mimes:xlsx,xls,xlx',
                'thr_tc_no' => 'required',
                'thr_name' => 'required',
                'thr_surname' => 'required',
                'thr_career_ladder' => 'required',
                'inst_id' => 'required',
                'thr_gender' => 'required',
                'thr_birth_day' => 'required',
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
                'thr_birth_day.required' => 'Doğum Tarihi alanı zorunludur.',
            ],
        );

        $params = $request->all();
        $uniqueKeyName = 'thr_tc_no';

        /* Excel satır sayılarının eşitliğnin kontrolü başla */
        $rowArrLetter = [];
        $rowArrNumber = [];
        foreach ($params as $key => $val) {
            if(!in_array($key, ['excel_file', 'updateDb', '_token', 'previewUniqueId', 'preview', 'enter'])) {
                $rowArr = explode('_', $val);

                if($key == $uniqueKeyName) {
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

        return true;
    }
    public function addExcel(Request $request)
    {
        // Teachers::whereNotIn('id', ['1'])->delete();
        $request->validate(
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
        );

        $params = $request->all();

        $preview = $params['preview'];
        unset($params['preview']);

        $previewUniqueId = $params['previewUniqueId'];
        unset($params['previewUniqueId']);
        
        /* $params['thr_tc_no'] = '0_2';
        $params['thr_name'] = '1_2';
        $params['thr_surname'] = '2_2';
        $params['thr_career_ladder'] = '13_2';
        $params['inst_id'] = '11_2';
        $params['thr_gender'] = '12_2'; */

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
                foreach ($updateArr as $key => $val) {
                    $tcNo = $val['thr_tc_no'];
                    unset($val['thr_tc_no']);
    
                    DB::table('teachers')
                        ->where('thr_tc_no', $tcNo)
                        ->update($val);
                }

                /* $updateArr = array_chunk($updateArr, 50);
    
                foreach ($updateArr as $updKey => $updVal) {
                    Teachers::insert($updVal);
                } */
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

        foreach ($params['images_file'] as $key => $val) {
            $tcNoArr[] = pathinfo($val->getClientOriginalName(), PATHINFO_FILENAME);
        }

        $teachers = Teachers::select('thr_tc_no')
            ->whereIn('thr_tc_no', $tcNoArr)
            ->get()
            ->toArray();

        $existTcNo = array_column($teachers, 'thr_tc_no');

        $fileUpload = new FileUpload();

        foreach ($params['images_file'] as $key => $val) {
            $tcNo = pathinfo($val->getClientOriginalName(), PATHINFO_FILENAME);
            $tcNo = intval($tcNo);
            
            if(in_array($tcNo, $existTcNo)) {
                $fileUpload->setConfig($val, null, 'JPG');
                $fileUpload->saveFile();

                $teacQuery = Teachers::where('thr_tc_no', $tcNo);
                $teach = $teacQuery->first();

                if($teach->thr_photo) {
                    Storage::delete('/public/upload/images/raw'.$teach->thr_photo);
                }

                $teacQuery->update(['thr_photo' => $fileUpload->getSavePath()]);
            }
        }

        $infoMsg = array_diff($tcNoArr, $existTcNo);
        if(count($infoMsg) > 0) {
            $infoMsg = join(', ', $infoMsg).' T.C Numara(ları) veritabanında bulunamadığından eklenemedi.';
        }else {
            $infoMsg = '';
        }

        return [
            'infoMsg' => $infoMsg,
            'succeed' => count($existTcNo) > 0 ? count($existTcNo).' Tane '.__('messages.add_success') : ''
        ];
    }

    public function storeWithMebbis(Request $request)
    {
        $request->validate(
            [
                'tc_no' => 'required|digits:11'
            ],
            [
                'tc_no.required' => 'Tc alanı zorunludur.',
                'tc_no.digit' => 'Tc alanı sadece rakamlardan ve 11 tane olmalıdır.',
            ],
        );

        $tcNo = $request->input('tc_no');

        $teacher = Teachers::where('thr_tc_no', $tcNo)->first();
        
        if($teacher) {
            throw ValidationException::withMessages(
                ['teacher' => 'Yüklü olan öğretmeni yükleyemezsiniz.']
            );
        }

        $mebbisBot = new \App\Library\MebBot\MebbisBot();

        $result = $mebbisBot->getTeacherWithTcNo($tcNo);

        Teachers::create($result);

        return [
            'succeed' => $tcNo.' T.C. Numarası '.__('messages.add_success')
        ];
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
            foreach ($updateArr as $key => $val) {
                $tcNo = $val['thr_tc_no'];
                unset($val['thr_tc_no']);

                DB::table('teachers')
                    ->where('thr_tc_no', $tcNo)
                    ->update($val);
            }

            /* $updateArr = array_chunk($updateArr, 50);
            
            foreach ($updateArr as $updKey => $updVal) {
                Teachers::insert($updVal);
            } */
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
        
        $provinces = array_flip($this->provinces);
        $towns = array_flip($this->towns);

        foreach ($datas as $updKey => $updVal) {
            $co++;
            if($co === $limit){
                break;
            }
            $instIndex = array_search($updVal['inst_id'], array_column($institutions, 'id'));
            $careerLadderIndex = strval($updVal['thr_career_ladder']) + 1;

            // var_dump(explode('_', $towns[$updVal['twn_id']])[0]);

            $tcNo       = $updVal['thr_tc_no'];
            $name       = $updVal['thr_name'];
            $surname    = $updVal['thr_surname'];
            $province   = empty($updVal['prv_id']) ? '': $provinces[$updVal['prv_id']];
            $town       = empty($updVal['twn_id']) ? '': explode('_', $towns[$updVal['twn_id']])[0];
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
        ->with('lawsuits')
        ->with('province')
        ->with('town')
        ->first();

        $date = explode('/', date('d/m/Y', $teacher->thr_birth_day));
        $date = empty($date[2]) ? 1000: $date[2];

        $tcValid = new ValidateTCNo(
            $teacher->thr_name ?? '', $teacher->thr_surname ?? '', $date ?? '' 
        );

        $tcValid = $tcValid->passes('thr_tc_no', $teacher->thr_tc_no);

        if(isset($teacher)) {
            foreach ($teacher->dc_documents as $key => $dc_documents) {
                $dc_documents->dcFiles;
                $dc_documents->dcAttachFiles;
    
                $user = User::select('name as user_name')->find($dc_documents->user_id);
                $dc_documents->user_name = $user->user_name;
            }
            
            foreach ($teacher->lawsuits as $key => $lawsuit) {
                $lawsuit->dc_document;
                $lawsuit->dc_document->dc_date = date("d/m/Y",$lawsuit->dc_document->dc_date);
                $lawsuit->dc_document->dcAttachFiles;
                $lawsuit->dc_document->dcFiles;
                $lawsuit->dc_documents;

                foreach ($lawsuit->dc_documents as $dc_key => $dc_val) {
                    $dc_val->dcFiles;
                    $dc_val->dcAttachFiles;
                    $dc_val->dc_date = date("d/m/Y",$dc_val->dc_date);
                }
                $lawsuit->subjects;
                $lawsuit->lawsuitFiles;
            }

            $teacher->thr_birth_day = date('d/m/Y', $teacher->thr_birth_day);
        }

        $request->flashOnly(['thr_tc_no']);

        $logInfo = new LogInfo('Öğretmen Modülü');
        $logInfo->crShowLog(
            "Gösterme::Öğretmenin Detay Bilgileri::{$teacher->thr_tc_no} {$teacher->thr_name} {$teacher->thr_surname} adlı öğretmenin bilgileri gösterildi"
        );

        return view(
            'admin.teachers.teacher_infos.teacher_infos',
            [
                'teacher' => $teacher ?? [],
                'tcValid' => $tcValid
            ]
        );
    }

    public function addLawFile(Request $request)
    {
        $request->validate(
            [
                'lawf_file_name' => 'required',
                'lawf_file_path' => 'required|unique:lawsuit_files,lawf_file_path',
                'dc_id' => 'required',
                'law_id' => 'required',
            ],
            [
                'lawf_file_name.required' => 'Lütfen dava dosyasının ismini giriniz.',
                'lawf_file_path.required' => 'Lütfen dava dosyasının yolunu giriniz.',
                'lawf_file_path.unique' => 'Dosya daha önce kaydedilmiş',
                'dc_id.required' => 'Lütfen dava dosyasına ait evrağı giriniz.',
                'law_id.required' => 'Lütfen dava dosyasına ait dava sayısını giriniz.',
            ],
        );

        $params = $request->all();

        $lawsuitFile = LawsuitFiles::create($params);

        return [
            'succeed' => __('messages.add_success'),
            'lawsuitFile' => $lawsuitFile
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\LawsuitFiles $lawsuitFile
     * @return \Illuminate\Http\Response
     */
    public function deleteLawFile(LawsuitFiles $lawsuitFile)
    {
        $res = $lawsuitFile->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('messages.delete_success');
        else
            $msg['error'] = __('messages.delete_error');

        return $msg;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    private function getSearchQuery($tblInfo, $dataList)
    {
        /* Arama Başla */
        if(!empty($tblInfo['thr_tc_no'])) {
            $dataList->where('t0.thr_tc_no', $tblInfo['thr_tc_no']);
        }
        
        if(!empty($tblInfo['thr_name'])) {
            $dataList->where('t0.thr_name', $tblInfo['thr_name']);
        }

        if(!empty($tblInfo['thr_surname'])) {
            $dataList->where('t0.thr_surname', $tblInfo['thr_surname']);
        }

        if(!empty($tblInfo['thr_email'])) {
            $dataList->where('t0.thr_email', $tblInfo['thr_email']);
        }

        if(!empty($tblInfo['thr_degree'])) {
            $dataList->where('t0.thr_degree', $tblInfo['thr_degree']);
        }

        if(!empty($tblInfo['thr_education_st'])) {
            $dataList->where('t0.thr_education_st', $tblInfo['thr_education_st']);
        }

        if(!empty($tblInfo['thr_place_of_task'])) {
            $dataList->where('t0.thr_place_of_task', $tblInfo['thr_place_of_task']);
        }

        if(isset($tblInfo['thr_career_ladder'])) {
            $dataList->where('t0.thr_career_ladder', $tblInfo['thr_career_ladder']);
        }

        if(!empty($tblInfo['prv_id'])) {
            $dataList->where('t0.prv_id', $tblInfo['prv_id']);
        }

        if(!empty($tblInfo['twn_id'])) {
            $dataList->where('t0.twn_id', $tblInfo['twn_id']);
        }

        if(!empty($tblInfo['thr_birth_day'])) {
            $date = str_replace('/', '-', $tblInfo['thr_birth_day']);
            $date = strtotime($date);

            $dataList->where('t0.thr_birth_day', $date);
        }
        /* Arama Bitiş */

        return $dataList;
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

        $dataList = $this->getSearchQuery($tblInfo, $dataList);

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

    public function exportExcelDatas(Request $request)
    {
        $params = $request->all();

        $dataList = DB::table('teachers as t0')
                        ->selectRaw('
                            thr_tc_no, thr_name, thr_surname, thr_gender,
                            thr_email, thr_career_ladder, thr_degree, 
                            thr_task, thr_education_st, thr_mobile_no,
                            thr_place_of_task, thr_photo, thr_birth_day,
                            t1.inst_name, t2.prv_name, t3.twn_name
                        ')
                        ->leftJoin('institutions as t1', 't1.id', '=', 't0.inst_id')
                        ->leftJoin('provinces as t2', 't2.id', '=', 't0.prv_id')
                        ->leftJoin('towns as t3', 't3.id', '=', 't0.twn_id');

        $dataList = $this->getSearchQuery($params, $dataList);
        $arrayData = $dataList->get()->toArray();

        $arrayData = array_map(function($item) {

            $date = date('d.m.Y', $item->thr_birth_day);

            switch ($item->thr_career_ladder) {
                case '0':
                    $careerLadder = "öğretmen";
                    break;

                case '1':
                    $careerLadder = "uzamn öğretmen";
                    break;
                    
                case '2':
                    $careerLadder = "başöğretmen";
                    break;
                
                default:
                    $careerLadder = "bilinmiyor";
                    break;
            }

            $gender = $item->thr_gender == "0" ? 'Erkek' : 'Bayan';

            return [
                "thr_tc_no" => $item->thr_tc_no,
                "thr_name" => $item->thr_name,
                "thr_surname" => $item->thr_surname,
                "thr_gender" => $gender,
                "thr_email" => $item->thr_email,
                "thr_career_ladder" => $careerLadder,
                "thr_degree" => $item->thr_degree,
                "thr_task" => $item->thr_task,
                "thr_education_st" => $item->thr_education_st,
                "thr_mobile_no" => $item->thr_mobile_no,
                "thr_place_of_task" => $item->thr_place_of_task,
                "thr_birth_day" => $date,
                "inst_name" => $item->inst_name,
                "prv_name" => $item->prv_name,
                "twn_name" => $item->twn_name
            ];
        }, $arrayData);

        array_unshift($arrayData , [
            "thr_tc_no" => 'Tc Kimlik No',
            "thr_name" => 'İsim',
            "thr_surname" => 'Soyisim',
            "thr_gender" => 'Cinsiyet',
            "thr_email" => 'Email',
            "thr_career_ladder" => 'Kariyer Basamağı',
            "thr_degree" => 'Ünvanı',
            "thr_task" => 'Görevi',
            "thr_education_st" => 'Öğrenim Durumu.',
            "thr_mobile_no" => 'Mobil No',
            "thr_place_of_task" => 'Görev Yeri',
            "thr_birth_day" => 'Doğum Tarihi',
            "inst_name" => 'Kurumu',
            "prv_name" => 'il',
            "twn_name" => 'İlçe'
        ]);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->fromArray($arrayData, NULL,'A1');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
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
     * @param  \App\Http\Requests\Admin\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = explode('/', $request->input('thr_birth_day'));
        $date = empty($date[2]) ? 1000: $date[2];

        $request->validate(
            [
                'thr_tc_no' => ['required', new ValidateTCNo(
                    $request->thr_name ?? '', $request->thr_surname ?? '', $date ?? '' 
                )],
                'thr_name' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
                'thr_surname' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
                'thr_career_ladder' => 'required|numeric',
                'inst_id' => 'required|integer',
                'thr_gender' => 'required|in:0,1',
                'thr_birth_day' => 'required',
            ],
            [
                'thr_tc_no.required' => 'Tc alanı zorunludur.',
                'thr_tc_no.digit' => 'Tc alanı sadece rakamlardan ve 11 tane olmalıdır.',
                'thr_name.required' => 'İsim alanı zorunludur.',
                'thr_name.regex' => 'İsim alanı sadece harflerden oluşmalıdır.',
                'thr_surname.required' => 'Soy isim alanı zorunludur.',
                'thr_surname.regex' => 'Soy isim alanı sadece harflerden oluşmalıdır.',
                'thr_career_ladder.required' => 'Kariyer basamağı alanı zorunludur.',
                'inst_id.required' => 'Kurum alanı zorunludur.',
                'thr_birth_day.required' => 'Doğum Tarihi alanı zorunludur.',
                'thr_gender.required' => 'Cinsiyet alanı zorunludur.',
                'thr_gender.in' => 'Cinsiyet sadece erkek veya bayan olmalıdır.',
            ],
        );

        $params = $request->all();

        $teacherExist = Teachers::where('thr_tc_no', $params['thr_tc_no']);

        if(!empty($teacherExist->first())) {
            throw ValidationException::withMessages(
                ['thr_tc_no' => "Aynı Tc'li veri ekleyemezsiniz."]
            );
        }

        $params['thr_birth_day'] = strtotime(str_replace('/', '-', $params['thr_birth_day']));
        $params['thr_name'] = \Transliterator::create('tr-title')->transliterate($params['thr_name']);
        $params['thr_surname'] = \Transliterator::create('tr-upper')->transliterate($params['thr_surname']);

        $teacher = Teachers::create($params);

        $logInfo = new LogInfo('Öğretmen Modülü');
        $logInfo->crCreateLog($teacher);

        return ['succeed' => __('messages.add_success')];
    }

    public function getProvincesList(Request $request)
    {
        $searchWords = $request->input('searchWords');
        $searchWords = $searchWords.'%';

        $provinces = DB::table('provinces as t0')
                        ->select('t0.id', 't0.prv_name as label')
                        ->whereRaw("t0.prv_name LIKE ?", $searchWords)
                        ->get();
        
        return $provinces;
    }

    public function getTownsList(Request $request)
    {
        $prvId = $request->input('prv_id');

        $towns = DB::table('towns as t0')
                        ->select('t0.id', 't0.twn_name as label')
                        ->whereRaw("t0.prv_id = ?", $prvId)
                        ->get();
        
        return $towns;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreTeachersRequest  $request
     * @return \Illuminate\Http\Response
     */
    /* public function store(StoreTeachersRequest $request)
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
    } */

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
        $teacher->thr_birth_day = date('d/m/Y', $teacher->thr_birth_day);
        $teacher->province;
        $teacher->town;
        return new isAjaxResponse($teacher);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\Request  $request
     * @param  \App\Models\Admin\Teachers  $teachers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teachers $teacher)
    {
        $oldTeach = clone $teacher;
        $date = explode('/', $request->input('thr_birth_day'));
        $date = empty($date[2]) ? 1000: $date[2];

        $request->validate(
            [
                'thr_tc_no' => ['required', new ValidateTCNo(
                    $request->thr_name ?? '', $request->thr_surname ?? '', $date ?? '' 
                )],
                'thr_name' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
                'thr_surname' => 'required|regex:/^[a-zA-ZğüşöçıİĞÜŞÖÇ ]+$/u',
                'thr_career_ladder' => 'required|numeric',
                'inst_id' => 'required|integer',
                'thr_gender' => 'required|in:0,1',
                'thr_birth_day' => 'required',
            ],
            [
                'thr_tc_no.required' => 'Tc alanı zorunludur.',
                'thr_tc_no.digit' => 'Tc alanı sadece rakamlardan ve 11 tane olmalıdır.',
                'thr_name.required' => 'İsim alanı zorunludur.',
                'thr_name.regex' => 'İsim alanı sadece harflerden oluşmalıdır.',
                'thr_surname.required' => 'Soy isim alanı zorunludur.',
                'thr_surname.regex' => 'Soy isim alanı sadece harflerden oluşmalıdır.',
                'thr_career_ladder.required' => 'Kariyer basamağı alanı zorunludur.',
                'inst_id.required' => 'Kurum alanı zorunludur.',
                'thr_birth_day.required' => 'Doğum Tarihi alanı zorunludur.',
                'thr_gender.required' => 'Cinsiyet alanı zorunludur.',
                'thr_gender.in' => 'Cinsiyet sadece erkek veya bayan olmalıdır.',
            ],
        );
        
        $params = $request->all();

        if($teacher->thr_tc_no !== $params['thr_tc_no']) {

            $teacherExist = Teachers::where('thr_tc_no', $params['thr_tc_no']);

            if(!empty($teacherExist->first())) {
                throw ValidationException::withMessages(
                    ['thr_tc_no' => "Aynı Tc'li veri ekleyemezsiniz."]
                );
            }
        }

        $params['thr_birth_day'] = strtotime(str_replace('/', '-', $params['thr_birth_day']));
        $params['thr_name'] = \Transliterator::create('tr-title')->transliterate($params['thr_name']);
        $params['thr_surname'] = \Transliterator::create('tr-upper')->transliterate($params['thr_surname']);

        $teacher->fill($params)->save();

        $logInfo = new LogInfo('Öğretmen Modülü');
        $logInfo->crUpdateLog($oldTeach, $teacher);

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
        $oldTeach = $teacher;

        if($teacher->thr_photo) {
            Storage::delete('/public/upload/images/raw'.$teacher->thr_photo);
        }

        $res = $teacher->delete();

        $logInfo = new LogInfo('Öğretmen Modülü');
        $logInfo->crDestroyLog($oldTeach);

        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
