<?php

namespace App\Http\Controllers\Admin\LawsuitManagement;

use Illuminate\Http\Request;
use App\Models\Admin\Lawsuits;
use App\Models\Admin\Subjects;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Admin\DcDocuments;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Shared\Html;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpWord\Style\Language;
use Illuminate\Support\Facades\Storage;
use App\Http\Responsable\isAjaxResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\LawsuitsManagement\StoreLawsuitsRequest;
use App\Http\Requests\Admin\LawsuitsManagement\UpdateLawsuitsRequest;
use App\Library\LogInfo;

class LawsuitsController extends Controller
{
    public function __construct() {
        $this->middleware(['permission:create lawsuits'])->only('store');
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
        /* echo '<pre>';
        var_dump($dt = strtotime('12.02.2022'));
        var_dump(date('Y', $dt));
        var_dump(date('d-m-Y', $dt));
        $dtt = strtotime('01.01.2023');
        var_dump($dtt);
        $yearEnd = date('d-m-Y', strtotime('01/01/2024'));
        dd($yearEnd);
        dd(date('d-m-Y', $dtt)); */
        /* $dirName = 'aile/uzman_sinav';
        $dirs = scandir(storage_path('app/public/'.$dirName.'/'));

        // dd($dirs);

        echo '<pre>';
        foreach ($dirs as $key => $val) {
            
            if($val != "." && $val != "..") {
                var_dump($val);
                $d = scandir(storage_path('app/public/'.$dirName.'/'.$val));

                foreach ($d as $dkey => $dval) {
                    if($dval != '.' && $dval != '..') {
                        $v = explode('_', $val);
                        $v = $v[1];
                        
                        $img = Image::make(storage_path('app/public/'.$dirName.'/'.$val.'/'.$dval));
                        Storage::put(
                            'public/'.$dirName.'_resim2/'.$v.'.JPG', 
                            $img->encode('jpg', 75)
                        );
                        // $img->save(storage_path('app/public/yok_uzman_muaf_resim/'), 80, 'jpg');
                    }
                }
                var_dump($d);
            }
        }
        echo '</pre>';

        dd($dirs); */
        /* // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $inputFileType = 'Xlsx';

        // $url = Storage::url('belge.xlsx');
        $url = storage_path('app/public/aile/bas_muaf.xlsx');

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        // $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($url);

        $filterSubset = new \App\Library\PhpOfficeSpreadsheetFilter(3,60,range('A','Z'));

        $datas = $spreadsheet->getActiveSheet()->toArray();
        echo '<pre>';
        $co = 0;
        foreach ($datas as $key => $value) {
            if(strlen($value[2]) == 11) {
                $co++;
                mkdir(storage_path('app/public/aile/bas_muaf/'.$co.'_'.$value[2]));
            }

            var_dump($value[0]);
            var_dump($value[1]);
            var_dump($value[2]);
            var_dump($value[3]);
            var_dump($value[4]);
            var_dump($value[5]);
            var_dump($value[6]);
            echo '<hr/>';
        }
        echo '</pre>';
        die; */
        
        return view('admin.lawsuits_mng.lawsuits.index');
    }

    public function lawInfos(Request $request)
    {
        $request->validate(
            [
                'law_id' => 'required',
                'law_id.*' => 'required|numeric'
            ],
            [
                'law_id.required' => 'Bilgi notu listesine en az bir dava eklemelisniz.',
                'law_id.*.required' => 'Bilgi notu listesine en az bir dava eklemelisniz.',
                'law_id.*.numeric' => 'Yanlış dava bilgisi lütfen sayfayı yenileyip yeniden deneyiniz.'
            ],
        );

        // dd($request->input('law_id'));
        $lawIds = $request->input('law_id');

        $lawsuits = Lawsuits::whereIn('id', $lawIds)
        ->with('union')
        ->with('subjects')
        ->get();

        $lawInfosArr = [];
        foreach ($lawsuits->toArray() as $lawKey => $lawVal) {
            if(array_key_exists($lawVal['union']['uns_name'], $lawInfosArr)) {
                
                $lawInfosArr[$lawVal['union']['uns_name']]['subjects'] = array_merge(
                    $lawInfosArr[$lawVal['union']['uns_name']]['subjects'], 
                    $lawVal['subjects']
                );
                
            }else {
                $lawInfosArr[$lawVal['union']['uns_name']]['subjects'] = $lawVal['subjects'];
            }
        }

        // dd($lawInfosArr);

        // dd($lawsuits);
        $alphabets = array(
            'a', 'b', 'c', 'ç', 'd', 'e', 'f', 'g', 
            'ğ', 'h', 'ı', 'i', 'j', 'k', 'l', 'm', 
            'n', 'o', 'ö', 'p', 'r', 's', 'ş','t', 
            'u', 'ü', 'v', 'y', 'z'
        );

        $phpWord = new PhpWord();

        $phpWord->getSettings()->setThemeFontLang(new Language(Language::TR_TR));
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $targetFile = storage_path('app/public/upload/lawsuitInfoWords/file1.docx');
        
        $section = $phpWord->addSection();

        $section->addText(
            "ADAY ÖĞRETMENLİK VE ÖĞRETMENLİK KARİYER BASAMAKLARI YÖNETMELİĞİNE VE YÖNERGEYE AÇILAN DAVALARA İLİŞKİN BİLGİ NOTU",
            array('name' => 'Times New Roman', 'size' => 12, 'bold'=> true),
            array('align' => 'center')
        );

        $html = "<br/><br/>";
        $lineCo = 0;
        foreach ($lawInfosArr as $lawInfoKey => $lawInfoVal) {
            $lineCo++;
            // $html .= "<br/>";
            
            if($lawInfoVal['subjects']) {
                
                if(count($lawInfoVal['subjects']) > 1) {

                    foreach ($lawInfoVal['subjects'] as $subKey => $subVal) {
                        if($subKey < 1) {
        $html .= "<p style='text-align: justify;'>
        <b>".$lineCo."- {$lawInfoKey} tarafından; </b>
        </p>";

        $html .= "<p style='text-align: justify;'>\t".$alphabets[$subKey].") {$subVal['sub_description']}
        </p>";
                        }else {
        $html .= "<p style='text-align: justify;'>\t".$alphabets[$subKey].") {$subVal['sub_description']}
        </p>";
                        }
                    }
                }else {
    $html .= "<p style='text-align: justify;'>
    <b>".$lineCo."- {$lawInfoKey} tarafından; </b> ".$lawInfoVal['subjects'][0]['sub_description']."
    </p>";
                }
            }
        }
        
        Html::addHtml($section, $html, false, true);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save($targetFile);
        } catch (\Exception $e) {
        }

        return response()->download($targetFile);
    }

    public function getLawBriefSearchList(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'searchName' => 'required|string'
            ],
            [
                'searchName.required' => 'Dava Kısa açıklaması giriniz.',
                'searchName.string' => 'Sadece harf giriniz.'
            ],
        );

        $params = $request->all();

        $lawsuits = Lawsuits::selectRaw('DISTINCT law_brief')
        ->whereRaw(
            'law_brief LIKE :searchName', 
            [
                'searchName' => '%'.$params['searchName'].'%'
            ]
        )
        ->get();

        $datas = array_map(function($lawsuit) {
            return [
                'id' => $lawsuit['law_brief'],
                'label' => $lawsuit['law_brief']
            ];
        }, $lawsuits->toArray());

        return $datas;
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
            'dc_base_number',
            'dc_date',
            'thr_ids',
            'uns_id',
            'dc_ids',
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

        $selectJoin = ", t1.dc_number, t1.dc_date, t1.dc_base_number";  

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
        $dataList->selectRaw('t2.thr_name, t2.thr_surname, t2.thr_tc_no');
        
        $dataList->leftJoin('unions as t3', 't3.id', '=', 't0.uns_id');
        $dataList->selectRaw('t3.uns_name');

        /* ARAMA BAŞLA */
        if(!empty($tblInfo['thr_ids'])) {
            $dataList->whereIn('t0.thr_id', $tblInfo['thr_ids']);
        }

        if(!empty($tblInfo['dc_ids'])) {
            $dataList->whereIn('t0.dc_id', $tblInfo['dc_ids']);
        }

        if(!empty($tblInfo['uns_id'])) {
            $dataList->where('t0.uns_id', $tblInfo['uns_id']);
        }

        if(!empty($tblInfo['dc_base_number'])) {
            $dataList->where('t1.dc_base_number', $tblInfo['dc_base_number']);
        }

        if(!empty($tblInfo['dc_date'])) {
            $vals = explode(" - ",$tblInfo['dc_date']);

            $vals = [
                strtotime(str_replace('/', '-', $vals[0])),
                strtotime(str_replace('/', '-', $vals[1])),
            ];

            $whereQuery = "t1.dc_date BETWEEN ? AND ?";
            $dataList->whereRaw($whereQuery, [$vals[0], $vals[1]]);
        }
        /* ARAMA BİTİŞ */

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
        }else {
            $subOrders = [];
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

        /* log info start */
        $logInfo = new LogInfo('Dava Modülü');
        $logData = [
            'benzersiz nmarası' => $lawsuit->id,
            'dava kısa açıklaması' => $lawsuit->law_brief,
            'dava ile ilişkilendirilen yazılar' => count($dcDownIds) > 0 
                                                    ? $downDcDocuments->pluck('dc_number')
                                                    : '',
            'dava konusunun maddeleri' => !empty($subjectArr) 
                                            ? collect($subjectArr)->pluck('sub_description')
                                            : ''
        ];

        $logInfo->crCreateLog($logData);
        /* log info end */

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
            'datas' => [
                'id' => $lawsuit->dc_document->id,
            ],
            'date' => date("d/m/Y", $lawsuit->dc_document->dc_date),
            'content' => $lawsuit->dc_document->dc_show_content,
            'baseNumber' => $lawsuit->dc_document->dc_base_number,
            'itemStatus' => $lawsuit->dc_document->dc_item_status == 0 
                ? 'Gelen Evrak'
                : 'Giden Evrak'
        ];

        if(!empty($lawsuit->dc_documents)) {
            $lawsuit->relDcDocuments = array_map(function($item) {
                return [
                    'datas' => [
                        'id' => $item['id'],
                    ],
                    'date' => date("d/m/Y", $item['dc_date']),
                    'content' => $item['dc_show_content'],
                    'baseNumber' => $item['dc_base_number'],
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
        $oldlaw = clone $lawsuit;
        $oldlaw->subjects;
        $oldlaw->dc_documents;
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

        $logInfo = new LogInfo('Dava Modülü');
        // $logInfo->crUpdateLog($oldlaw, $lawsuit);
        // dd(collect($subjectArr));
        $logInfo->crUpdateLog(
            [
                'benzersiz nmarası' => $oldlaw->id,
                'dava kısa açıklaması' => $oldlaw->law_brief,
                'dava konusunun maddeleri' => count($oldlaw->subjects) > 0 
                                                ? $oldlaw->subjects->pluck('sub_description')
                                                : '',
                'dava ile ilişkilendirilen yazılar' => count($oldlaw->dc_documents) > 0 
                                                        ? $oldlaw->dc_documents->pluck('dc_number')
                                                        : '',
            ],
            [
                'benzersiz nmarası' => $lawsuit->id,
                'dava kısa açıklaması' => $lawsuit->law_brief,
                'dava konusunun maddeleri' => !empty($subjectArr) 
                                                ? collect($subjectArr)->pluck('sub_description')
                                                : '',
                'dava ile ilişkilendirilen yazılar' => count($dcDownIds) > 0 
                                                        ? $downDcDocuments->pluck('dc_number')
                                                        : ''
            ]
        );

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
        $lawsuit->subjects;
        $lawsuit->dc_documents;
        $oldlaw = $lawsuit;
        
        $res = $lawsuit->delete();
        $msg = [];

        $logInfo = new LogInfo('Dava Modülü');
        $logInfo->crDestroyLog([
            'benzersiz nmarası' => $oldlaw->id,
            'dava kısa açıklaması' => $oldlaw->law_brief,
            'dava konusunun maddeleri' => count($oldlaw->subjects) > 0 
                                            ? $oldlaw->subjects->pluck('sub_description')
                                            : '',
            'dava ile ilişkilendirilen yazılar' => count($oldlaw->dc_documents) > 0 
                                                    ? $oldlaw->dc_documents->pluck('dc_number')
                                                    : '',
        ]);

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
