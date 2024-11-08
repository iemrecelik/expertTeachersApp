<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Library\FileUpload;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\DcFiles;
use App\Models\Admin\DcLists;
use App\Models\Admin\Teachers;
use App\Models\Admin\DcComment;
use App\Models\Admin\DcCategory;
use App\Models\Admin\DcDocuments;
use App\Models\Admin\DcAttachFiles;
use App\Models\Admin\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\DocumentManagement\StoreDcDocumentsRequest;
use App\Http\Requests\Admin\DocumentManagement\StoreManualDcDocumentsRequest;
use App\Http\Requests\Admin\DocumentManagement\UpdateDcDocumentsRequest;
use App\Library\LogInfo;
use App\Models\Admin\DcArchives;

class DocumentsController extends Controller
{
    public function getDocumentSearchList(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'dcNumber' => 'required|integer'
            ],
            [
                'dcNumber.required' => 'Evrak Numarasını giriniz.',
                'dcNumber.integer' => 'Sadece rakam giriniz.'
            ],
        );

        $params = $request->all();

        $dcDocuments = DcDocuments::selectRaw('id, dc_number, dc_date, dc_item_status, dc_show_content, dc_base_number')
        ->whereRaw(
            'dc_number LIKE :dcNumber', 
            [
                'dcNumber' => $params['dcNumber'].'%'
            ]
        )
        ->get();

        $datas = array_map(function($dcDocument) {
            $dcFilePath = DcFiles::select('dc_file_path')
                ->where('dc_file_owner_id', $dcDocument['id'])
                ->get()
                ->toArray();
            
            return [
                'id' => $dcDocument['id'],
                'label' => $dcDocument['dc_number'],
                'date' => date("d/m/Y", $dcDocument['dc_date']),
                'baseNumber' => $dcDocument['dc_base_number'],
                'itemStatus' => $dcDocument['dc_item_status'] == 0?'Gelen Evrak':'Giden Evrak',
                'content' => $dcDocument['dc_show_content'],
                'path' => $dcFilePath[0]['dc_file_path']
            ];
        }, $dcDocuments->toArray());

        // $datas['rawDatas'] = $dcDocuments->toArray();

        return $datas;
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.document_mng.create_document');
    }

    /**
     * Show the form for manual creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manualCreate()
    {
        return view('admin.document_mng.create_document');
    }

    public function udfControl(Request $request)
    {
        if($request->hasFile('dc_sender_file')) {
            
            $name = 'dc_sender_file';

        }else if($request->hasFile('rel_dc_sender_file')) {
            
            $file = $request->file();

            $index = array_keys($file['rel_dc_sender_file']);
            
            $name = "rel_dc_sender_file.{$index[0]}";
        }

        $file = $request->file($name);
        $arr = null;

		$pattern = "/^.*\.(udf)$/i";
		preg_match($pattern, $file->getClientOriginalName(), $orjExtension);
        		
		if(!empty($orjExtension[1])) {
            $arr = $this->getFileInfos($request);
		}
        
        $pattern = "/^.*\.(pdf|PDF)$/i";
		preg_match($pattern, $file->getClientOriginalName(), $externalAgencyExt);

        if(!empty($externalAgencyExt[1])) {
            $parser = new Parser();

            $pdf = $parser->parseFile($file->getPathName());
            $content = $pdf->getText();

            // dd($content);
        
            $content = $this->changeTurkishCharecter($content);
            $content = $this->replaceAllSpaceChar($content);

            // $arr['content'] = $pdf->getText();
            $arr['content'] = $content;
		}

        $pattern = "/^.*\.(tif|TIF)$/i";
		preg_match($pattern, $file->getClientOriginalName(), $externalAgencyExt);

        if(!empty($externalAgencyExt[1])) {
            $arr['manuelCreate'] = true;
            $arr['content'] = '';
		}
        
		return $arr;
    }

    private function setParamsIntoArr(Array $params, Request $request, Int $main, Int $key = null)
    {
        if($main > 0) {
            $arr = [
                'dc_number'         => trim($params['dc_number']),
                'dc_item_status'    => $params['dc_item_status'],
                'dc_main_status'    => "1",
                'dc_cat_id'         => $params['dc_cat_id'],
                'dc_subject'        => $params['dc_subject'],
                'dc_who_send'       => $params['dc_who_send'],
                'dc_who_receiver'   => $params['dc_who_receiver'],
                'dc_base_number'    => $params['dc_base_number'],
                'dc_content'        => $params['dc_content'] ?? '',
                'dc_show_content'   => $params['dc_show_content'] ?? '',
                'dc_raw_content'    => $params['dc_raw_content'] ?? '',
                'dc_date'           => strtotime($params['dc_date']),
                'user_id'           => $request->user()->id,
                'list_id'           => $params['list_id'],
                'thr_id'            => $params['thr_id'] ?? null,
                'dc_com_text'       => $params['dc_com_text'],
                'dc_manuel'         => $params['dc_manuel'],
            ];
        }else {
            $arr = [
                'dc_number'         => trim($params['rel_dc_number'][$key]),
                'dc_item_status'    => $params['rel_dc_item_status'][$key],
                // 'dc_cat_id'         => $params['dc_cat_id'],
                'dc_cat_id'         => $params['rel_dc_cat_id'][$key],
                'dc_subject'        => $params['rel_dc_subject'][$key],
                'dc_who_send'       => $params['rel_dc_who_send'][$key],
                'dc_who_receiver'   => $params['rel_dc_who_receiver'][$key],
                'dc_base_number'    => $params['rel_dc_base_number'][$key],
                'dc_content'        => $params['rel_dc_content'][$key] ?? '',
                'dc_show_content'   => $params['rel_dc_show_content'][$key] ?? '',
                'dc_raw_content'    => $params['rel_dc_raw_content'][$key] ?? '',
                'dc_date'           => strtotime($params['rel_dc_date'][$key]),
                'user_id'           => $request->user()->id,
                'dc_manuel'         => $params['dc_manuel'],

                'list_id'           => $params['rel_list_id'][$key],
                'thr_id'            => $params['rel_thr_id'][$key] ?? null,
                'dc_com_text'       => $params['rel_dc_com_text'][$key],
            ];   
        }

        return $arr;
    }

    /**
     * Form request validation.
     *
     * @param  StoreManualDcDocumentsRequest  $request
     * @return bool
     */
    public function validateManuelStore(StoreManualDcDocumentsRequest $request)
    {
        $params = $request->all();

        $this->sameDocumentControl($params);

        $arr = $this->setParamsIntoArr($params, $request, 1);

        if ($request->hasFile('dc_sender_file')) {

            $pattern = "/^.*\.(udf)$/i";
            preg_match(
                $pattern, 
                $request->file('dc_sender_file')->getClientOriginalName(),
                $orjExtension
            );

            if(!empty($orjExtension[1])) {
                /* İmza kontrolü yapma başla */
                $this->signatureControl($request->file('dc_sender_file'));
                /* İmza kontrolü yapma bitiş */

                $this->fileContentCtrl($request->file('dc_sender_file'), $arr);
            }
        }

        if (isset($params['rel_dc_number'])) {
            
            foreach ($params['rel_dc_number'] as $key => $val) {
                if($request->hasFile('rel_dc_sender_file')) {

                    if (isset($request->file('rel_dc_sender_file')[$key])) {

                        $pattern = "/^.*\.(udf)$/i";
                        preg_match(
                            $pattern, 
                            $request->file('rel_dc_sender_file')[$key]->getClientOriginalName(),
                            $orjExtension
                        );

                        if(!empty($orjExtension[1])) {
                            $relArr = $this->setParamsIntoArr($params, $request, 0, $key);

                            /* İmza kontrolü yapma başla */
                            $this->signatureControl($request->file('rel_dc_sender_file')[$key]);
                            /* İmza kontrolü yapma bitiş */

                            $this->fileContentCtrl($request->file('rel_dc_sender_file')[$key], $relArr);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Store a newly manual created resource in storage.
     *
     * @param  StoreManualDcDocumentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function manualStore(StoreManualDcDocumentsRequest $request)
    {
        $params = $request->all();

        // dd($params);

        $this->sameDocumentControl($params);

        $arr = $this->setParamsIntoArr($params, $request, 1);
        
        /* $arr = [
            'dc_number'         => trim($params['dc_number']),
            'dc_item_status'    => $params['dc_item_status'],
            'dc_main_status'    => "1",
            'dc_cat_id'         => $params['dc_cat_id'],
            'dc_subject'        => $params['dc_subject'],
            'dc_who_send'       => $params['dc_who_send'],
            'dc_who_receiver'   => $params['dc_who_receiver'],
            'dc_content'        => $params['dc_content'] ?? '',
            'dc_show_content'   => $params['dc_show_content'] ?? '',
            'dc_raw_content'    => $params['dc_raw_content'] ?? '',
            'dc_date'           => strtotime($params['dc_date']),
            'user_id'           => $request->user()->id,
            'list_id'           => $params['list_id'],
            'thr_id'            => $params['thr_id'] ?? null,
            'dc_com_text'       => $params['dc_com_text'],
            'dc_manuel'         => $params['dc_manuel'],
        ]; */

        /* Dosya içeriği ile girilen bilgilerin benzerliğini kontrol etme başla */
        if ($request->hasFile('dc_sender_file')) {

            $pattern = "/^.*\.(udf)$/i";
            preg_match(
                $pattern, 
                $request->file('dc_sender_file')->getClientOriginalName(),
                $orjExtension
            );

            if(!empty($orjExtension[1])) {
                /* İmza kontrolü yapma başla */
                $this->signatureControl($request->file('dc_sender_file'));
                /* İmza kontrolü yapma bitiş */

                $this->fileContentCtrl($request->file('dc_sender_file'), $arr);
            }
        }

        if (isset($params['rel_dc_number'])) {
            
            foreach ($params['rel_dc_number'] as $key => $val) {
                if($request->hasFile('rel_dc_sender_file')) {

                    if (isset($request->file('rel_dc_sender_file')[$key])) {

                        $pattern = "/^.*\.(udf)$/i";
                        preg_match(
                            $pattern, 
                            $request->file('rel_dc_sender_file')[$key]->getClientOriginalName(),
                            $orjExtension
                        );

                        if(!empty($orjExtension[1])) {
                            $relArr = $this->setParamsIntoArr($params, $request, 0, $key);

                            /* İmza kontrolü yapma başla */
                            $this->signatureControl($request->file('rel_dc_sender_file')[$key]);
                            /* İmza kontrolü yapma bitiş */

                            $this->fileContentCtrl($request->file('rel_dc_sender_file')[$key], $relArr, true);
                        }
                    }
                }
            }
        }
        /* Dosya içeriği ile girilen bilgilerin benzerliğini kontrol etme bitiş */

        $dcDocuments = $this->saveDcDocument(
            $arr, 
            [
                $request->file('dc_sender_file')
            ], 
            $request->file('dc_sender_attach_files'),
        );

        /* Save Relative Document */
        $this->manualSaveRelDocument($dcDocuments, $params, $request);

        if(isset($params['add_dc_number_id'])) {
            foreach ($params['add_dc_number_id'] as $key => $val) {
                $dcRel = DcDocuments::find($val);
                $dcDocuments->dc_ralatives()->save($dcRel);

                $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crShowLog(
                    "Ekleme::Evrak Ekleme::<b>{$dcDocuments->dc_number}</b> sayısına <b>{$dcRel->dc_number}</b> sayılı yazı ilişkilendirildi."
                );
            }
        }

        $msg = ['succeed' => __('messages.edit_success')];
        
        return redirect()->route('admin.document_mng.document.create')
                        ->with($msg);
    }

    private function manualSaveRelDocument($dcDocuments, $params, $request)
    {
        if (isset($params['rel_dc_number'])) {
            
            foreach ($params['rel_dc_number'] as $key => $val) {

                $arr = $this->setParamsIntoArr($params, $request, 0, $key);

                /* $arr = [
                    'dc_number'         => trim($params['rel_dc_number'][$key]),
                    'dc_item_status'    => $params['rel_dc_item_status'][$key],
                    'dc_cat_id'         => $params['dc_cat_id'],
                    'dc_subject'        => $params['rel_dc_subject'][$key],
                    'dc_who_send'       => $params['rel_dc_who_send'][$key],
                    'dc_who_receiver'   => $params['rel_dc_who_receiver'][$key],
                    'dc_content'        => $params['rel_dc_content'][$key] ?? '',
                    'dc_show_content'   => $params['rel_dc_show_content'][$key] ?? '',
                    'dc_raw_content'    => $params['rel_dc_raw_content'][$key] ?? '',
                    'dc_date'           => strtotime($params['rel_dc_date'][$key]),
                    'user_id'           => $request->user()->id,
                    'dc_manuel'         => $params['dc_manuel'],
                ]; */

                $relDcSenderAttachFiles = $request->file('rel_dc_sender_attach_files');
                $relDcSenderAttachFiles = isset($relDcSenderAttachFiles[$key]) ? 
                                            $relDcSenderAttachFiles[$key] : 
                                            null;

                $this->saveDcDocument(
                    $arr,
                    [
                        $request->file('rel_dc_sender_file')[$key]
                    ],
                    $relDcSenderAttachFiles,
                    $dcDocuments
                );
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreDcDocumentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    public function store(StoreDcDocumentsRequest $request)
    {
        $params = $request->all();

        $this->sameDocumentControl($params);
        
        $arr = [
            'dc_number'         => trim($params['dc_number']),
            'dc_item_status'    => $params['dc_item_status'],
            'dc_main_status'    => "1",
            'dc_cat_id'         => $params['dc_cat_id'],
            'dc_subject'        => $params['dc_subject'],
            'dc_who_send'       => $params['dc_who_send'],
            'dc_who_receiver'   => $params['dc_who_receiver'],
            'dc_content'        => $params['dc_content'],
            'dc_show_content'   => $params['dc_show_content'],
            'dc_raw_content'    => $params['dc_raw_content'],
            'dc_date'           => strtotime($params['dc_date']),
            'user_id'           => $request->user()->id,
        ];

        $dcDocuments = $this->saveDcDocument(
            $arr, 
            [
                $request->file('dc_sender_file')
            ], 
            $request->file('dc_sender_attach_files'),
        );

        /* Save Relative Document */
        if (isset($params['rel_dc_number'])) {
            
            foreach ($params['rel_dc_number'] as $key => $val) {

                $arr = [
                    'dc_number'         => trim($params['rel_dc_number'][$key]),
                    'dc_item_status'    => $params['rel_dc_item_status'][$key],
                    'dc_cat_id'         => $params['dc_cat_id'],
                    'dc_subject'        => $params['rel_dc_subject'][$key],
                    'dc_who_send'       => $params['rel_dc_who_send'][$key],
                    'dc_who_receiver'   => $params['rel_dc_who_receiver'][$key],
                    'dc_content'        => $params['rel_dc_content'][$key],
                    'dc_show_content'   => $params['rel_dc_show_content'][$key],
                    'dc_raw_content'    => $params['rel_dc_raw_content'][$key],
                    'dc_date'           => strtotime($params['rel_dc_date'][$key]),
                    'user_id'           => $request->user()->id,
                ];

                $relDcSenderAttachFiles = $request->file('rel_dc_sender_attach_files');
                $relDcSenderAttachFiles = isset($relDcSenderAttachFiles[$key]) ? 
                                            $relDcSenderAttachFiles[$key] : 
                                            null;

                $this->saveDcDocument(
                    $arr,
                    [
                        $request->file('rel_dc_sender_file')[$key]
                    ],
                    $relDcSenderAttachFiles,
                    $dcDocuments
                );
            }
        }

        $msg = ['succeed' => __('messages.edit_success')];
        
        return redirect()->route('admin.document_mng.document.create')
                        ->with($msg);
    }

    private function sameDocumentControl($params)
    {
        if(isset($params['add_dc_number_id'])) {
            $ex = DcDocuments::whereIn('id', $params['add_dc_number_id'])->get();

            if($ex->count() < 1) {
                throw ValidationException::withMessages(
                    [
                        'add_dc_number_id' => '
                            Eklediğiniz evraklardan biri veri tabanında bulunamadı. Siz eklerken silinmiş olabilir. 
                            Lütfen tekrardan ilişkilendirip yükleyiniz.
                        '
                    ]
                );
            }
        }

        $rel_dc_number = [];
        if(isset($params['rel_dc_number'])) {
            /* $existDc = DcDocuments::whereIn('dc_number', $params['rel_dc_number'])->get();

            if($existDc->count() > 0) {
                throw ValidationException::withMessages(
                    [
                        'relDcNumber' => '
                            Yüklü olan evrağı yeniden yükleyemezsiniz. 
                            Yüklü dosyalardan ilgiye ekleyiniz.
                        '
                    ]
                );
            } */

            foreach ($params['rel_dc_number'] as $key => $val) {
                
                $year = date('Y', strtotime($params['rel_dc_date'][$key]));

                $existDc = DcDocuments::where('dc_number', $val)
                            ->whereBetween('dc_date', [strtotime('01.01.'.$year), strtotime('31.12.'.$year)])
                            ->get();

                if($existDc->count() > 0) {
                    throw ValidationException::withMessages(
                        [
                            'relDcNumber' => '
                                İlgiye eklemek istediğiniz "'.$existDc[0]->dc_number.'" sayılı evrak zaten yüklü. 
                                Lütfen yüklü dosyalardan ilgiye ekleyiniz.
                            '
                        ]
                    );
                }
                
                if(trim($params['dc_number']) == trim($params['rel_dc_number'][$key])) {
                    throw ValidationException::withMessages(
                        ['senderFile' => 'ilgi yazı ile ana evrak aynı olamaz']
                    );
                }

                if (array_search($params['rel_dc_number'], $rel_dc_number) === false) {
                    $rel_dc_number[] = $params['rel_dc_number'][$key];
                } else {
                    throw ValidationException::withMessages(
                        ['senderFile' => 'ilgi yazılar aynı evrak olamaz.']
                    );
                }
            }
        }
    }

    private function saveDcDocument($params, $dcFile, $dcAttachFiles = null, $dcDocuments = null)
    {
        $listId = $params['list_id'] ?? 0;
        $teacherIds = $params['thr_id'] ?? [];
        $dcComText = $params['dc_com_text'] ?? '';
        $year = date('Y', $params['dc_date']);
        $catIds = $params['dc_cat_id'] ? $params['dc_cat_id'] : $params['rel_dc_cat_id'];

        unset($params['list_id']);
        unset($params['thr_id']);
        unset($params['dc_com_text']);
        unset($params['dc_cat_id']);   
        unset($params['rel_dc_cat_id']);   

        if(empty($dcDocuments)) {
            
            $dcDocuments = DcDocuments::where([
                ['dc_number', $params['dc_number']],
                ['dc_main_status', "1"],
            ])
            ->whereBetween('dc_date', [strtotime('01.01.'.$year), strtotime('31.12.'.$year)])
            ->first();

            if(!empty($dcDocuments)) {
                throw ValidationException::withMessages(
                    ['document' => 'Yüklenmeye çalışılan evrak zaten mevcuttur.']
                );
            }

            /* $dcDocuments = DcDocuments::where(
                ['dc_number' => $params['dc_number']],
            )
            ->whereBetween('dc_date', [strtotime('01.01.'.$year), strtotime('31.12.'.$year)])
            ->first(); */

            $exist = empty($dcDocuments) ? false : true;
            
            if ($exist === false) {
                $dcDocuments = DcDocuments::create(
                    // ['dc_number' => array_shift($params)],
                    $params
                );
            }/* else {
                $dcDocuments->dc_main_status = "1";
                $dcDocuments->save();
            } */
            
            /* Kategorileri ekleme başla*/
            $categories = DcCategory::whereIn('id', $catIds)->get();
            $dcDocuments->dcCategories()->saveMany($categories);
            /* Kategorileri ekleme bitiş*/

            /* Listeye ekleme */
            if($listId > 0) {
                $dcList = DcLists::find($listId);

                $dcDocuments->dc_lists()->save($dcList);
            }

            /* Öğretmenleri ekleme */
            if(count($teacherIds) > 0) {
                $teachers = Teachers::whereIn('id', $teacherIds)->get();

                $dcDocuments->dc_teachers()->saveMany($teachers);

                $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crShowLog(
                    "Ekleme::Evrak Ekleme::"."<b>".json_encode($teachers->pluck('thr_tc_no'), JSON_UNESCAPED_UNICODE)."</b> ilgili(lere) <b>{$dcDocuments->dc_number}</b> sayılı yazı ilişkilendirildi."
                );
            }

            /* Dökümana not ekleme */
            if(!empty($dcComText)) {

                $dcComment = DcComment::create([
                    'dc_com_text'   => $dcComText,
                    'dc_id'         => $dcDocuments->id,
                    'user_id'       => $params['user_id'],
                ]);
            }

            $this->uploadFile([
                'dcDocuments'   => $dcDocuments,
                'params'        => $params,
                'dcFile'        => $dcFile,
                'dcAttachFiles' => $dcAttachFiles,
                'exist'         => $exist,
            ]);
            
        }else {
            $dcRelative = DcDocuments::where(
                ['dc_number' => $params['dc_number']]
            )
            ->whereBetween('dc_date', [strtotime('01.01.'.$year), strtotime('31.12.'.$year)])
            ->first();

            $exist = empty($dcRelative) ? false : true;

            if($exist === false) {
                $dcRelative = DcDocuments::create(
                    // ['dc_number' => array_shift($params)],
                    $params
                );
            }

            $dcDocuments->dc_ralatives()->save($dcRelative);

            $logInfo = new LogInfo('Evrak Ekleme');
            $logInfo->crShowLog(
                "Ekleme::Evrak Ekleme::<b>{$dcDocuments->dc_number}</b> sayısına <b>{$dcRelative->dc_number}</b> sayılı yazı ilişkilendirildi."
            );

            /* Kategorileri ekleme başla*/
            // dd($catIds);
            $categories = DcCategory::whereIn('id', $catIds)->get();
            $dcRelative->dcCategories()->saveMany($categories);
            /* Kategorileri ekleme bitiş*/


            /* Listeye ekleme */
            if($listId > 0) {
                $dcList = DcLists::find($listId);

                $dcRelative->dc_lists()->save($dcList);
            }

            /* Öğretmenleri ekleme */
            if(count($teacherIds) > 0) {
                $teachers = Teachers::whereIn('id', $teacherIds)->get();

                $dcRelative->dc_teachers()->saveMany($teachers);

                $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crShowLog(
                    "Ekleme::Evrak Ekleme::"."<b>".json_encode($teachers->pluck('thr_tc_no'), JSON_UNESCAPED_UNICODE)."</b> ilgili(lere) <b>{$dcRelative->dc_number}</b> sayılı yazı ilişkilendirildi."
                );
            }

            /* Dökümana not ekleme */
            if(!empty($dcComText)) {

                $dcComment = DcComment::create([
                    'dc_com_text'   => $dcComText,
                    'dc_id'         => $dcRelative->id,
                    'user_id'       => $params['user_id'],
                ]);
            }

            $this->uploadFile([
                'dcDocuments'   => $dcRelative,
                'params'        => $params,
                'dcFile'        => $dcFile,
                'dcAttachFiles' => $dcAttachFiles,
                'exist'         => $exist,
            ]);
        }

        $logInfo = new LogInfo('Evrak Ekleme');
        $logInfo->crCreateLog([
            'sayı' => $dcDocuments->dc_number,
            'konu' => $dcDocuments->dc_subject,
            'kime' => $dcDocuments->dc_who_receiver,
            'gönderen' => $dcDocuments->dc_who_send,
        ]);

        return $dcDocuments;
    }
    
    public function uploadFile($arr)
    {
        extract($arr);
        
        if($dcFile[0]) {
            if($exist === true) {
                Storage::delete($dcDocuments->dcFiles->dc_file_path);
                $dcDocuments->dcFiles()->delete();
            }

            $filesArr = $this->saveFileToStorage(
                $dcFile, 
                'DcFiles', 
                'dc_file_path',
                // 'udf'
            );
        }

        $dcAttachFilesCollection = $dcDocuments->dcAttachFiles()
                ->whereNotIn('id', $dcUploadedAttachFiles ?? []);

        $dcAttachFilesItems = $dcAttachFilesCollection->get();
        $dcAttachFilesCollection->delete();
        

        $this->deleteImageFromStorage($dcAttachFilesItems);

        // dd($dcAttachFilesItems);
        if(isset($dcAttachFiles)) {

            $attachFilesArr = $this->saveFileToStorage(
                $dcAttachFiles,
                'DcAttachFiles',
                'dc_att_file_path'
            );
        }
            
        /* New images will be saved to database */
        if(isset($filesArr))
            $dcDocuments->dcFiles()->saveMany($filesArr);
        
        if(isset($attachFilesArr))
            $dcDocuments->dcAttachFiles()->saveMany($attachFilesArr);
    }

    private function saveFileToStorage($files, $modelName, $filePath, $extension = null)
    {
        $fileUpload = new FileUpload();

        foreach ($files as $key => $fileVal) {

            $fileUpload->setConfig($fileVal, null, $extension, true);

            $fileUpload->saveFile();
        }

        $filesArr = array_map(function($path) use ($modelName, $filePath){

            $modelName = "App\Models\Admin\\".$modelName;

            $path = str_replace('public', 'storage', $path);
            return new $modelName([$filePath => $path]);

        }, $fileUpload->getSavePaths());

        return $filesArr;
    }

    private function deleteImageFromStorage($oldImages)
    {
        $deleteImgs = [];

        foreach ($oldImages as $oldImage) {
            $path = $oldImage->dc_att_file_path;

            $deleteImgs[] = "public/upload/images/raw{$path}";
        }

        Storage::delete($deleteImgs);
    }

    private function signatureControl($file)
    {
        try {
            $sign = file_get_contents("zip://{$file->getPathName()}#sign.sgn");
        } catch (\Throwable $th) {
            throw ValidationException::withMessages(
                ['signature' => 'Lütfen dys tarafından onaylanmış dosya yükleyiniz.']
            );
        }

        // dd(file_get_contents("zip://{$file->getPathName()}#content.xml"));

        $datas = $this->getFileContent(
            file_get_contents("zip://{$file->getPathName()}#content.xml")
        );

        // dd($datas);

        extract($datas);

        if(!empty($number[1])) {
            $pattern = '/20299769/si';
            preg_match($pattern, trim($number[1]), $existOygm);
        }else {
            throw ValidationException::withMessages(
                ['signature' => 'Lütfen dys tarafından onaylanmış dosya yükleyiniz.']
            );
        }

        /* $pattern = '/Öğretmen Yetiştirme ve Geliştirme Genel Müdürlüğü/si';
        preg_match($pattern, $sender[1], $existOygm); */

        $setting = Settings::select('set_auth_signature_names')->find(1);

        // dd($setting);

        if(count($existOygm) > 0 || empty(trim($number[1]))) {
            // $pattern = '/mahmut özer|MAHMUT ÖZER|Mahmut Özer|PETEK AŞKAR|Petek Aşkar|petek aşkar|CEVDET VURAL|Cevdet Vural|cevdet vural|NECAT ALTIOK|Necat Altıok|necat altıok|AYŞE OĞUZ|Ayşe Oğuz|ayşe oğuz|UFUK DİLEKÇİ|Ufuk Dilekçi|ufuk dilekçi/si';
            $pattern = '/'.$setting->set_auth_signature_names.'/si';

            preg_match($pattern, $sign, $existSignature);

            if(count($existSignature) < 1) {
                throw ValidationException::withMessages(
                    ['signature' => '
                        Yetkili personelin imzasıyla uyuşmamaktadır.
                    ']
                );
            }
        }
    }

    private function fileContentCtrl($file, $params, $dump = false)
    {
        $datas = $this->getFileContent(
            file_get_contents("zip://{$file->getPathName()}#content.xml")
        );

        extract($datas);
        // dd($datas);
        /* if ($dump) {
            dd($datas);
        } */

        /* dc_number kontrolü başla */
        /* $pattern = '/'.$params['dc_number'].'/si';
        preg_match($pattern, $number[2], $exist); */

        $none = $params['dc_number'] !== trim($number[2]);

        if($none) {
            throw ValidationException::withMessages(
                ['exist' => '
                    Girdiğiniz evrak sayısı yüklediğniz evrak bilgileri ile uyuşmamaktadır. 
                    Lütfen doğru bilgileri giriniz.'
                ]
            );
        }
        /* dc_number kontrolü bitiş */
        
        /* dc_date kontrolü başla */
        /* $pattern = '/'.date('d.m.Y', $params['dc_date']).'/si';

        preg_match($pattern, $number[3], $exist); */

        $none = $number[3] !== date('d.m.Y', $params['dc_date']);

        if($none) {
            throw ValidationException::withMessages(
                ['exist' => '
                    Girdiğiniz tarih sayısı yüklediğniz evrak bilgileri ile uyuşmamaktadır. 
                    Lütfen doğru bilgileri giriniz.'
                ]
            );
        }
        /* dc_date kontrolü bitiş */
        
        /* dc_who_send kontrolü başla */
        $senderMain = \Transliterator::create('tr-upper')->transliterate(trim($params['dc_who_send']));
        $senderSearch = \Transliterator::create('tr-upper')->transliterate(trim($sender[1]));

        $senderSearch = preg_replace("/\s+/", " ", $senderSearch);
        $senderSearch = trim($senderSearch);

        $senderMain = preg_replace("/\s+/", " ", $senderMain);
        $senderMain = trim($senderMain);

        $pattern = '/(.)+ (.)+/si';
        preg_match($pattern, trim($params['dc_who_send']), $exist);

        $none = strlen($params['dc_who_send']) > 5 && count($exist) > 0
                    ? stripos($senderSearch, $senderMain) >= 0
                    : false;

                    /* echo '<pre>';
                    var_dump($senderSearch);
                    var_dump($senderMain);
                    dd(stripos($senderSearch, $senderMain)); */

        if(!$none) {
            throw ValidationException::withMessages(
                ['exist' => '
                    Girdiğiniz gönderici bilgisi yüklediğiniz evrak bilgileri ile uyuşmamaktadır. 
                    Lütfen doğru bilgileri giriniz.'
                ]
            );
        }
        /* dc_who_send kontrolü bitiş */

        /* evrak gönderim durumu kontrolü başla */
        $pattern = '/20299769/si';
        preg_match($pattern, trim($number[1]), $exist);
        
        

        $none = (count($exist) > 0 && $params['dc_item_status'] == "1") || (count($exist) < 1 && $params['dc_item_status'] == "0");
        /* if($dump) {
            echo '<pre>';
            var_dump($params['dc_item_status']);
            var_dump($exist);
            dd($none);
        } */
        
        if(!$none) {
            throw ValidationException::withMessages(
                ['exist' => '
                    Girdiğiniz evrak durum bilgisi evrak bilgileri ile uyuşmamaktadır. 
                    Lütfen doğru bilgileri giriniz.'
                ]
            );
        }
        /* evrak gönderim durumu kontrolü bitiş */

        /* dc_who_receiver kontrolü başla */
        $receiverMain = \Transliterator::create('tr-upper')->transliterate(trim($params['dc_who_receiver']));
        $receiverSearch = \Transliterator::create('tr-upper')->transliterate(trim($receiver[1]));

        $receiverSearch = preg_replace("/\s+/", " ", $receiverSearch);
        $receiverSearch = trim($receiverSearch);

        $receiverMain = preg_replace("/\s+/", " ", $receiverMain);
        $receiverMain = trim($receiverMain);

        $pattern = '/(.)+ (.)+/si';
        preg_match($pattern, trim($params['dc_who_receiver']), $exist);

        $none = strlen($params['dc_who_receiver']) > 5 && count($exist) > 0
                    ? stripos($receiverSearch, $receiverMain) >= 0
                    : false;

        if(!$none) {
            throw ValidationException::withMessages(
                ['exist' => '
                    Girdiğiniz alıcı bilgisi yüklediğiniz evrak bilgileri ile uyuşmamaktadır. 
                    Lütfen doğru bilgileri giriniz.'
                ]
            );
        }
        /* dc_who_receiver kontrolü bitiş */
    }

    private function getFileContent($result)
    {
        $pattern = '/<!\[CDATA\[\¸(.*)\n{1,10}sayı/si';
        preg_match($pattern, $result, $sender);

        if(count($sender) > 0) {
            // $pattern = '/konu\s*:.*([A-ZİĞÜŞÖÇ ]{10,1000}\n{2,10})/si';
            // $pattern = '/konu\s*?:(.*?)\n{2,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{2,10}(.+)]]>/si';
            $pattern = '/konu\s*?:(.*?)\n{2,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}|[A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{2,10}(.+)]]>/si';
            preg_match($pattern, $result, $receiver);

            // dd($receiver);

            if(count($receiver) < 1 || strlen($receiver[1]) > 200 || $receiver[1] == '' || $receiver[2] == '' || $receiver[3] == '') {
                $pattern = '/konu\s*?:(.*?)\n{1,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}|[A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{2,10}(.+)]]>/si';
                preg_match($pattern, $result, $receiver);
            }else if(count($receiver) < 1 || strlen($receiver[1]) > 200 || $receiver[1] == '' || $receiver[2] == '' || $receiver[3] == '') {
                $pattern = '/konu\s*?:(.*?)\n{1,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}|[A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{1,10}(.+)]]>/si';
                preg_match($pattern, $result, $receiver);
            }

            // dd($receiver);

            $pattern = '/<!\[CDATA\[\¸(.*)]]>/si';
            preg_match($pattern, $result, $content);

            // $pattern = '/sayı\s*?:(.*-)(\d*)\s([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})\n/si';
            $pattern = '/sayı\s*?:(.*-)([\d\/\(\)\.e]*)\s([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
            preg_match($pattern, $result, $number);

            // dd($number);

            if(count($number) < 1 || $number[1] == '' || $number[2] == '' || $number[3] == '' ) {
                $pattern = '/sayı\s*?:(.*[-|\/])([\d\/\(\)\.e]*)\t([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
                preg_match($pattern, $result, $number);
            }
            if(count($number) < 1 || $number[1] == '' || $number[2] == '' || $number[3] == '' ) {
                $pattern = '/sayı\s*?:(.*[-|\/])([\d\/\(\)\.e\[\]]*)\t([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
                preg_match($pattern, $result, $number);
            }
        }else {
            $pattern = '/\<content\>.*(T\.C\..*)\n{1,10}sayı/si';
            preg_match($pattern, $result, $sender);

            /* if(strlen($sender[1] > 100)) {

            } */

            $pattern = '/konu\s*?:(.*?)[\n\s]{2,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}|[A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{2,10}(.+)\<\/content\>/si';
            preg_match($pattern, $result, $receiver);

            if(count($receiver) < 1 || strlen($receiver[1] || $receiver[1] == '' || $receiver[2] == '' || $receiver[3] == '' ) > 100) {
                $pattern = '/konu\s*?:(.*?)\n{1,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}|[A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{2,10}(.+)\<\/content\>/si';
                preg_match($pattern, $result, $receiver);
            }else if(count($receiver) < 1 || strlen($receiver[1]) > 100 || $receiver[1] == '' || $receiver[2] == '' || $receiver[3] == '') {
                $pattern = '/konu\s*?:(.*?)\n{1,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}|[A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{1,10}(.+)\<\/content\>/si';
                preg_match($pattern, $result, $receiver);
            } 
            
            $pattern = '/\<content\>.*(T\.C\..*)\<\/content\>/si';
            preg_match($pattern, $result, $content);

            // $pattern = '/sayı\s*?:(.*-)([\d\/\(\)]*)\s([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})\n/si';
            // $pattern = '/sayı\s*?:(.*-|\.)([\d\/\(\)]*)\s([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
            $pattern = '/sayı\s*?:(.*-)([\d\/\(\)\.e]*)\s([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
            preg_match($pattern, $result, $number);

            // dd($number);

            if(count($number) < 1 || $number[1] == '' || $number[2] == '' || $number[3] == '' ) {
                $pattern = '/sayı\s*?:(.*[-|\/])([\d\/\(\)\.e]*)\t([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
                preg_match($pattern, $result, $number);
            }else if(count($number) < 1 || $number[1] == '' || $number[2] == '' || $number[3] == '' ) {
                $pattern = '/sayı\s*?:(.*[-|\/])([\d\/\(\)\.e]*)\t([0-9]{1,2}[\., \/][0-9]{1,2}[\., \/][0-9]{4})\n/si';
                preg_match($pattern, $result, $number);
            }
        }

        /* dd([
            'sender' => $sender,
            'receiver' => $receiver,
            'content' => $content,
            'number' => $number,
        ]); */

        $number[3] = str_replace('/', '.', $number[3]);
        
        if(isset($receiver[3])) {
            $receiver[3] = preg_replace('/\n/', '<br/>', $receiver[3]);
            $receiver[3] = preg_replace('/\t{3,100}/', '<span class="mr-5"></span>', $receiver[3]);
            $receiver[3] = preg_replace('/\t/', '<span class="mr-5"></span>', $receiver[3]);
        }
        
        $content[1] = $this->replaceAllSpaceChar($content[1]);

        /* preg_match_all('!\d+!', $number[2], $matches);
        $number[2] = $matches[0][0]; */

        if(strpos($number[2], ')') !== false) {
            $number[2] = explode(')', $number[2]);
            $number[2] = end($number[2]);
        }

        if(strpos($number[2], ']') !== false) {
            $number[2] = explode(']', $number[2]);
            $number[2] = end($number[2]);
        }

        preg_match_all('!\d+!', $number[2], $matches);
        $number[2] = end($matches[0]);

        // dd($matches);

        /* dd([
            'sender' => $sender,
            'receiver' => $receiver,
            'content' => $content,
            'number' => $number,
        ]); */

        return [
            'sender' => $sender,
            'receiver' => $receiver,
            'content' => $content,
            'number' => $number,
        ];
    }

    private function changeTurkishCharecter($content)
    {
        // 'Â-â-î-Û-û'
        $old = ['ý', 'Ý', 'þ', 'Þ', 'ð', 'Ð'];
        $new = ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ'];

        $content = str_replace(
            $old,
            $new,
            $content
        );

        return $content;
    }

    private function replaceSpace($string)
    {
        $string = preg_replace("/\s+/", " ", $string);
        $string = trim($string);
        return $string;
    }

    private function replaceAllSpaceChar($text)
    {
        // $text = preg_replace('/\n/', '', $text);
        $text = preg_replace('/\t/', '', $text);
        
        $text = $this->replaceSpace($text);

        return $text;
    }

    public function getBotFileInfos($path)
    {
        $path = str_replace('/', '\\', $path);
        $path = storage_path('app\public\upload\images\raw'.$path);

        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $content = $pdf->getText();
        
        $content = $this->changeTurkishCharecter($content);
        $content = $this->replaceAllSpaceChar($content);

        return $content;
    }

    public function getBotFileInfos2($path)
    {
        $path = str_replace('/', '\\', $path);
        
        $path = storage_path('app\public\upload\images\raw'.$path);
        
        $result = file_get_contents("zip://{$path}#content.xml");
        
        try {
            $datas = $this->getFileContent($result);

            extract($datas);

            $showContent = $this->createShowContentHtml([
                'sender'    => $sender,
                'number'    => $number,
                'receiver'  => $receiver,
            ]);

            if (
                empty($sender[1]) || empty($number[1]) || 
                empty($number[2]) || empty($number[3]) ||
                empty($receiver[1]) || empty($receiver[2])
            ) {
                throw ValidationException::withMessages(
                    [
                        'senderFile' => 'Yazım formatı hatalı lütfen manual giriş yapınız.',
                        'manuel' => true,
                        'content' => $content[1]
                    ],
                );
            }

        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw ValidationException::withMessages(
                [
                    'senderFile' => 'Yazım formatı hatalı lütfen manual giriş yapınız.',
                    'manuel' => true,
                    'content' => $content[1]
                ],
            );
        }

        if(
            (strlen($receiver[1]) > 255) || !is_numeric($number[2]) ||
            (strlen($sender[1]) > 255) || (strlen($receiver[2]) > 255)
        ) {
            throw ValidationException::withMessages(
                [
                    'senderFile' => 'Yazım formatı hatalı lütfen manual giriş yapınız.',
                    'manuel' => true,
                    'content' => $content[1]
                ],
            );
        }

        $arr = [
            'sender' => trim($sender[1]),
            'subjectNumber' => trim($number[1]),
            'number' => trim($number[2]),
            'date' => trim($number[3]),
            'subject' => trim($receiver[1]),
            'content' => $content[1],
            'rawContent' => $result,
            'receiver' => trim($receiver[2]),
            'showContent' => $showContent,
        ];

        return $arr;
    }

    public function getFileInfos(Request $request)
    {
        if($request->hasFile('dc_sender_file')) {
            
            $name = 'dc_sender_file';

        }else if($request->hasFile('rel_dc_sender_file')) {
            
            $file = $request->file();

            $index = array_keys($file['rel_dc_sender_file']);
            
            $name = "rel_dc_sender_file.{$index[0]}";
        }

        $request->validate([
            'rel_dc_sender_file.*' => 'required|mimes:zip|max:2048',
        ]);

        $file = $request->file($name);

        /* İmza kontrolü yapma başla */
        $this->signatureControl($file);
        /* İmza kontrolü yapma bitiş */

        $result = file_get_contents("zip://{$file->getPathName()}#content.xml");

        try {
            $datas = $this->getFileContent($result);
            // dd($datas);
            extract($datas);

            /* dd([
                'sender'    => $sender,
                'number'    => $number,
                'receiver'  => $receiver,
            ]); */

            $showContent = $this->createShowContentHtml([
                'sender'    => $sender,
                'number'    => $number,
                'receiver'  => $receiver,
            ]);

            if (
                empty($sender[1]) || empty($number[1]) || 
                empty($number[2]) || empty($number[3]) ||
                empty($receiver[1]) || empty($receiver[2])
            ) {
                throw ValidationException::withMessages(
                    [
                        'senderFile' => 'Dosya formatı hatalı manuel giriş yapınız.',
                        'manuel' => true,
                        'content' => $content[1]
                    ]
                );
            }

        } catch (\Throwable $th) {

            throw ValidationException::withMessages(
                [
                    'senderFile' => 'Dosya formatı hatalı manuel giriş yapınız.',
                    'manuel' => true,
                    'content' => $content[1]
                ]
            );
        }

        if(
            (strlen($receiver[1]) > 255) || !is_numeric($number[2]) ||
            (strlen($sender[1]) > 255) || (strlen($receiver[2]) > 255)
        ) {
            throw ValidationException::withMessages(
                [
                    'senderFile' => 'Yazım formatı hatalı lütfen manuel giriş yapınız.',
                    'manuel' => true,
                    'content' => $content[1]
                ],
            );
        }

        /* Evrak Durumu başla */
        $number[1] = str_replace('/', '-', $number[1]);
        $itemStatus = explode('-', trim($number[1]));
        $itemStatus = $itemStatus[1] == '20299769' ? '1' : '0';
        /* Evrak Durumu bitiş */

        $arr = [
            'sender' => trim($sender[1]),
            'subjectNumber' => trim($number[1]),
            'itemStatus' => trim($itemStatus),
            'number' => trim($number[2]),
            'date' => trim($number[3]),
            'subject' => trim($receiver[1]),
            'content' => $content[1],
            'rawContent' => $result,
            'receiver' => trim($receiver[2]),
            'showContent' => $showContent,
        ];

        return $arr;
    }

    private function createShowContentHtml($datas)
    {
        extract($datas);
        
        $showContent = '
        <div class="row mb-5">
            <div class="col-4"></div>
            <div class="col-4 text-center">
                        '.$sender[1].'
            </div>
            <div class="col-4"></div>
            </div>

        <div class="row">
            <div class="col-1"></div>
            <div class="col-9">
                Sayı: '.$number[1].$number[2].'
            </div>
            <div class="col-2">
                '.$number[3].'
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-1"></div>
            <div class="col-11">
                Konu: '.$receiver[1].'
            </div>
        </div>
        

        <div class="row mb-5">
            <div class="col-4"></div>
            <div class="col-4 text-center">
                '.$receiver[2].'
            </div>
            <div class="col-4"></div>
        </div>

        <div class="row mb-5">
            <div class="col-1"></div>
            <div class="col-10">
                '.$receiver[3].'
            </div>
            <div class="col-1"></div>
        </div>
        ';

        return $showContent;
    }

    public function edit(DcDocuments $document)
    {
        $document->dcFiles;
        $document->dcAttachFiles;
        $document->dc_ralatives;
        $document->dc_lists;
        $document->dc_teachers;
        $document->dcCategories;

        /* timestamp verisini tarih formatına çevirme başla */
        $document->dc_date = date('d.m.Y', $document->dc_date);
        foreach ($document->dc_ralatives as $key => $val) {
            $val->dc_date = date('d.m.Y', $val->dc_date);
            $val->dcFiles;
            $val->dcAttachFiles;
        }
        /* timestamp verisini tarih formatına çevirme bitiş */

        return view(
            'admin.document_mng.edit_document',
            ['data' => $document]
        );
    }

    private function updateDcDocument($params, $dcFile = null, $dcAttachFiles = null, $dcDocuments = null, $dcUploadedAttachFiles = null)
    {
        $listId = $params['list_id'] ?? 0;
        $teacherIds = $params['thr_id'] ?? [];
        $dcComText = $params['dc_com_text'] ?? '';
        $id = $params['id'];
        $year = date('Y', $params['dc_date']);
        // $catIds = $params['dc_cat_id'];
        $catIds = $params['dc_cat_id'] ? $params['dc_cat_id'] : $params['rel_dc_cat_id'];

        unset($params['list_id']);
        unset($params['thr_id']);
        unset($params['dc_com_text']);
        unset($params['id']);
        unset($params['dc_cat_id']);
        unset($params['rel_dc_cat_id']);
        
        if(empty($dcDocuments)) {
            
            $dcDocuments = DcDocuments::where([
                ['id', $id],
                ['dc_main_status', "1"],
            ])->first();
            
            if(empty($dcDocuments)) {
                throw ValidationException::withMessages(
                    [
                        'document' => '
                            Düzenlemeye çalışılan evrak mevcut değildir. 
                            Başkası tarafından silinmiş olabilir. 
                            Lütfen evrağı yeniden seçiniz.
                        '
                    ]
                );
            }

            $oldDcDocuments = clone $dcDocuments;

            $dcDocumentExist = DcDocuments::where([
                ['id', '!=', $id],
                ['dc_number', '=', $params['dc_number']],
                ['dc_main_status', "1"],
            ])
            ->whereBetween('dc_date', [strtotime('01.01.'.$year), strtotime('31.12.'.$year)])
            ->first();

            if($dcDocumentExist) {
                throw ValidationException::withMessages(
                    [
                        'document' => 'Yeni yazdığınız evrak sayısı zaten yüklü. Lütfen dosya sayısını değiştiriniz.'
                    ]
                );
            }

            $params = array_filter($params, fn($value) => !is_null($value) && $value !== '');
            $dcDocuments->fill($params);

            $dcDocuments->save();

            $logInfo = new LogInfo('Evrak Ekleme');
            $logInfo->crUpdateLog(
                [
                    'sayı' => $oldDcDocuments->dc_number,
                    'konu' => $oldDcDocuments->dc_subject,
                    'kime' => $oldDcDocuments->dc_who_receiver,
                    'gönderen' => $oldDcDocuments->dc_who_send,
                ], 
                [
                    'sayı' => $dcDocuments->dc_number,
                    'konu' => $dcDocuments->dc_subject,
                    'kime' => $dcDocuments->dc_who_receiver,
                    'gönderen' => $dcDocuments->dc_who_send,
                ]
            );

            $exist = true;

            /* Listeye ekleme */
            if($listId > 0) {
                $dcDocuments->dc_lists()->detach();

                $dcList = DcLists::find($listId);
                $dcDocuments->dc_lists()->save($dcList);
            }

            /* Öğretmenleri ekleme */
            if(count($teacherIds) > 0) {
                $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crShowLog(
                    "Silme::Evrak Ekleme::"."<b>".json_encode($dcDocuments->dc_teachers->pluck('thr_tc_no'), JSON_UNESCAPED_UNICODE)."</b> ilgili(ler) den <b>{$dcDocuments->dc_number}</b> sayılı yazı ilişikten kaldırıldı."
                );

                $dcDocuments->dc_teachers()->detach();

                $teachers = Teachers::whereIn('id', $teacherIds)->get();
                $dcDocuments->dc_teachers()->saveMany($teachers);

                // $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crShowLog(
                    "Ekleme::Evrak Ekleme::"."<b>".json_encode($teachers->pluck('thr_tc_no'), JSON_UNESCAPED_UNICODE)."</b> ilgili(lere) <b>{$dcDocuments->dc_number}</b> sayılı yazı ilişkilendirildi."
                );
            }else {
                $dcDocuments->dc_teachers()->detach();
            }

            /* İzinli kullanıcıları ekleme */

            /* Dökümana not ekleme */
            if(!empty($dcComText)) {

                DcComment::where('dc_id', $dcDocuments->id)->delete();

                DcComment::create([
                    'dc_com_text'   => $dcComText,
                    'dc_id'         => $dcDocuments->id,
                    'user_id'       => $params['user_id'],
                ]);
            }

            /* Kategorileri ekleme başla*/
            $dcDocuments->dcCategories()->detach();
            $categories = DcCategory::whereIn('id', $catIds)->get();
            $dcDocuments->dcCategories()->saveMany($categories);
            /* Kategorileri ekleme bitiş*/

            /* İlgi evrakları sil */
            // if(isset($dcDocuments->dc_ralatives->dc_number)) {
            $logInfo = new LogInfo('Evrak Ekleme');
            $logInfo->crShowLog(
                "Silme::Evrak Ekleme::<b>{$dcDocuments->dc_number}</b> sayısından <b>{$dcDocuments->dc_ralatives->pluck('dc_number')}</b> sayılı yazı ilişikten kaldırıldı."
            );
            // }

            $dcDocuments->dc_ralatives()->detach();

            $this->uploadFile([
                'dcDocuments'           => $dcDocuments,
                'params'                => $params,
                'dcFile'                => $dcFile,
                'dcAttachFiles'         => $dcAttachFiles,
                'exist'                 => $exist,
                'dcUploadedAttachFiles' => $dcUploadedAttachFiles ?? [],
            ]);
            
        }else {
            $dcRelative = DcDocuments::where(
                ['id' => $id]
            )->first();

            $exist = empty($dcRelative) ? false : true;

            if($exist === false) {
                $dcRelative = DcDocuments::create(
                    $params
                );

                $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crCreateLog([
                    'sayı' => $dcRelative->dc_number,
                    'konu' => $dcRelative->dc_subject,
                    'kime' => $dcRelative->dc_who_receiver,
                    'gönderen' => $dcRelative->dc_who_send,
                ]);
            }else {
                $params = array_filter($params, fn($value) => !is_null($value) && $value !== '');
                $dcRelative->fill($params);
                $dcRelative->save();
            }

            $dcDocuments->dc_ralatives()->save($dcRelative);

            $this->uploadFile([
                'dcDocuments'           => $dcRelative,
                'params'                => $params,
                'dcFile'                => $dcFile,
                'dcAttachFiles'         => $dcAttachFiles,
                'exist'                 => $exist,
                'dcUploadedAttachFiles' => $dcUploadedAttachFiles ?? [],
            ]);
        }

        /* Günceleme süresini ve kişiyi ekleme başla */
        $user = auth()->user();
        $date = date("d-m-Y H:i:s");

        $dcDocuments->updated_by = $user->id;
        $dcDocuments->updated_by_name = "$date | $user->name ($user->email)";
        $dcDocuments->save();
        /* Günceleme süresini ve kişiyi ekleme bitiş */

        $dcArchive = DcArchives::where('dc_arc_number', $dcDocuments->dc_number)->first();
        $dcArchive->delete();

        return $dcDocuments;
    }

    private function setUpdateParamsIntoArr(Array $params, Request $request, Int $main, Int $key = null)
    {
        if($main > 0) {
            $arr = [
                'id'                => $params['id'],
                'dc_number'         => trim($params['dc_number']),
                'dc_item_status'    => $params['dc_item_status'],
                'dc_main_status'    => "1",
                'dc_cat_id'         => $params['dc_cat_id'],
                'dc_subject'        => $params['dc_subject'],
                'dc_who_send'       => $params['dc_who_send'],
                'dc_who_receiver'   => $params['dc_who_receiver'],
                'dc_base_number'    => $params['dc_base_number'],
                'dc_content'        => $params['dc_content'] ?? '',
                'dc_show_content'   => $params['dc_show_content'] ?? '',
                'dc_raw_content'    => $params['dc_raw_content'] ?? '',
                'dc_date'           => strtotime($params['dc_date']),
                // 'user_id'           => $request->user()->id,
                'list_id'           => $params['list_id'] ?? null,
                'thr_id'            => $params['thr_id'] ?? null,
                'dc_com_text'       => $params['dc_com_text'] ?? null,
                'dc_manuel'         => $params['dc_manuel'],
            ];
        }else {
            $arr = [
                'id'                => $params['rel_dc_id'][$key],
                'dc_number'         => trim($params['rel_dc_number'][$key]),
                'dc_item_status'    => $params['rel_dc_item_status'][$key],
                // 'dc_cat_id'         => $params['dc_cat_id'],
                'dc_cat_id'         => $params['rel_dc_cat_id'][$key],
                'dc_subject'        => $params['rel_dc_subject'][$key],
                'dc_who_send'       => $params['rel_dc_who_send'][$key],
                'dc_who_receiver'   => $params['rel_dc_who_receiver'][$key],
                'dc_base_number'    => $params['rel_dc_base_number'][$key],
                'dc_content'        => $params['rel_dc_content'][$key] ?? '',
                'dc_show_content'   => $params['rel_dc_show_content'][$key] ?? '',
                'dc_raw_content'    => $params['rel_dc_raw_content'][$key] ?? '',
                'dc_date'           => strtotime($params['rel_dc_date'][$key]),
                // 'user_id'           => $request->user()->id,
                'dc_manuel'         => $params['dc_manuel'],
            ];
        }

        return $arr;
    }

    /**
     * Store a newly manual created resource in storage.
     *
     * @param  UpdateDcDocumentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDcDocumentsRequest $request)
    {
        $params = $request->all();

        $this->sameDocumentControl($params);

        $arr = $this->setUpdateParamsIntoArr($params, $request, 1);

        /* Dosya içeriği ile girilen bilgilerin benzerliğini kontrol etme başla */
        if ($request->hasFile('dc_sender_file')) {
            $pattern = "/^.*\.(udf)$/i";
            preg_match(
                $pattern, 
                $request->file('dc_sender_file')->getClientOriginalName(),
                $orjExtension
            );

            if(!empty($orjExtension[1])) {
                $this->fileContentCtrl($request->file('dc_sender_file'), $arr);

                /* İmza kontrolü yapma başla */
                $this->signatureControl($request->file('dc_sender_file'));
                /* İmza kontrolü yapma bitiş */
            }
        }

        if (isset($params['rel_dc_number'])) {

            foreach ($params['rel_dc_number'] as $key => $val) {
                if($request->hasFile('rel_dc_sender_file')) {

                    if (isset($request->file('rel_dc_sender_file')[$key])) {

                        $pattern = "/^.*\.(udf)$/i";
                        preg_match(
                            $pattern, 
                            $request->file('rel_dc_sender_file')[$key]->getClientOriginalName(),
                            $orjExtension
                        );

                        if(!empty($orjExtension[1])) {
                            $relArr = $this->setUpdateParamsIntoArr($params, $request, 0, $key);
                        
                            /* İmza kontrolü yapma başla */
                            $this->signatureControl($request->file('rel_dc_sender_file')[$key]);
                            /* İmza kontrolü yapma bitiş */

                            $this->fileContentCtrl($request->file('rel_dc_sender_file')[$key], $relArr);
                        }
                    }
                }
            }
        }
        /* Dosya içeriği ile girilen bilgilerin benzerliğini kontrol etme bitiş */

        $dcDocuments = $this->updateDcDocument(
            $arr, 
            [
                $request->file('dc_sender_file')
            ], 
            $request->file('dc_sender_attach_files'),
            null,
            $params['dc_uploaded_sender_attach_files'] ?? null
        );

        /* Save Relative Document */
        $this->updateRelDocument($dcDocuments, $params, $request);

        if(isset($params['add_dc_number_id'])) {
            foreach ($params['add_dc_number_id'] as $key => $val) {
                $dcRel = DcDocuments::find($val);
                $dcDocuments->dc_ralatives()->save($dcRel);

                $logInfo = new LogInfo('Evrak Ekleme');
                $logInfo->crShowLog(
                    "Ekleme::Evrak Ekleme::<b>{$dcDocuments->dc_number}</b> sayısına <b>{$dcRel->dc_number}</b> sayılı yazı ilişkilendirildi."
                );
            }
        }
        
        $msg = ['succeed' => __('messages.edit_success')];
        
        return redirect()->route('admin.document_mng.document.edit', $params['id'])
                        ->with($msg);
    }

    private function updateRelDocument($dcDocuments, $params, $request)
    {
        if (isset($params['rel_dc_number'])) {
            
            foreach ($params['rel_dc_number'] as $key => $val) {

                $arr = [
                    'id'                => $params['rel_dc_id'][$key],
                    'dc_number'         => trim($params['rel_dc_number'][$key]),
                    'dc_item_status'    => $params['rel_dc_item_status'][$key],
                    'dc_cat_id'         => $params['dc_cat_id'],
                    'dc_subject'        => $params['rel_dc_subject'][$key],
                    'dc_who_send'       => $params['rel_dc_who_send'][$key],
                    'dc_who_receiver'   => $params['rel_dc_who_receiver'][$key],
                    'dc_base_number'    => $params['rel_dc_base_number'][$key],
                    'dc_content'        => $params['rel_dc_content'][$key] ?? '',
                    'dc_show_content'   => $params['rel_dc_show_content'][$key] ?? '',
                    'dc_raw_content'    => $params['rel_dc_raw_content'][$key] ?? '',
                    'dc_date'           => strtotime($params['rel_dc_date'][$key]),
                    'user_id'           => $request->user()->id,
                    'dc_manuel'         => $params['dc_manuel'],
                ];

                $relDcSenderFiles = $request->file('rel_dc_sender_file');
                $relDcSenderFiles = isset($relDcSenderFiles[$key]) ? 
                                        $relDcSenderFiles[$key] : 
                                        null;
                                            
                $relDcSenderAttachFiles = $request->file('rel_dc_sender_attach_files');
                $relDcSenderAttachFiles = isset($relDcSenderAttachFiles[$key]) ? 
                                            $relDcSenderAttachFiles[$key] : 
                                            null;

                $relDcUploadedSenderAttachFiles = isset($params['rel_dc_uploaded_sender_attach_files'][$key]) ?
                                                    $params['rel_dc_uploaded_sender_attach_files'][$key] :
                                                    null;

                $this->updateDcDocument(
                    $arr,
                    [
                        $relDcSenderFiles
                    ],
                    $relDcSenderAttachFiles,
                    $dcDocuments,
                    $relDcUploadedSenderAttachFiles
                );
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcDocuments $document
     * @return \Illuminate\Http\Response
     */
    public function deleteDocument(DcDocuments $document)
    {
        $oldDocument = $document;
        if(isset($document->dcFiles->dc_file_path)) {
            Storage::delete($document->dcFiles->dc_file_path);
            $document->dcFiles()->delete();
        }

        foreach ($document->dcAttachFiles as $attachKey => $attachVal) {
            Storage::delete($attachVal->dc_file_path);
        }

        if(count($document->dcAttachFiles) > 0) {
            $document->dcAttachFiles()->delete();
        }

        $dcArchive = DcArchives::where('dc_arc_number', $document->dc_number)->first();
        $dcArchive->delete();

        $res = $document->delete();

        $logInfo = new LogInfo('Evrak Ekleme');
        $logInfo->crDestroyLog([
            'sayı' => $oldDocument->dc_number,
            'konu' => $oldDocument->dc_subject,
            'kime' => $oldDocument->dc_who_receiver,
            'gönderen' => $oldDocument->dc_who_send,
        ]);

        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }

    private function pdfReplaceSpace($string)
    {
        $spCount = substr_count($string, ' ');

        /* if($spCount > 100) {
            // $string = str_replace(' ', '', $string);
            $string = preg_replace("/\t/", "&nbsp&nbsp", $string);
            $string = str_repeat('&nbsp;', 100).trim($string);
        } */
        // $string = "Learn PHP at HowToCodeSchool.com";
        // $string = str_replace(' ', '&nbsp;', $string);
        // echo $string;
        // $string = preg_replace("/\s+/", " ", $string);
        // $string = preg_replace("/\n/", "", $string);
        // $string = preg_replace("/\t/", " ", $string);
        // $string = preg_replace("/\t/", "&nbsp&nbsp", $string);
        // $string = trim($string);
        return $string;
    }

    public function privewUdfToPdf(DcDocuments $document)
    {
        $html = $this->udfXmlReader($document->dcFiles->dc_file_path);

        // echo $html;die;

        $mpdf = New \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir'), 'format' => 'A4' ]);
        // $mpdf = New \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir'), 'format' => [300, 300] ]);
        $mpdf->SetTitle($document->dc_number.' Sayılı Yazı');
        
        // $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    private function udfXmlReader($path)
    {
        // dd($path);
        // $path = storage_path('app\public\upload\\'.$path);

        $path = str_replace('/', '\\', $path);
        
        $path = storage_path('app\public\upload\images\raw'.$path);
        $path = "zip://{$path}#content.xml";
        
        /* $result = file_get_contents("zip://{$path}#content.xml");
        dd($result); */

        $xml = new \DOMDocument();
        $xml->load($path);

        /* echo '<pre style="white-space: pre-wrap;
        white-space: -moz-pre-wrap;
        white-space: -pre-wrap;
        white-space: -o-pre-wrap;
        word-wrap: break-word;">'; */
        $contentEl = $xml->getElementsByTagName("content");
        /* foreach ($contentEl as $contVal) {
            foreach($contVal->childNodes as $child) {
                if ($child->nodeType == XML_CDATA_SECTION_NODE) {
                    $cdata = $child->textContent;
                }
            }
        } */

        foreach($contentEl[0]->childNodes as $child) {
            if ($child->nodeType == XML_CDATA_SECTION_NODE) {
                $cdata = $child->textContent;
            }else {
                $cdata = $child->textContent;
            }
        }

        $paragraph = $xml->getElementsByTagName('paragraph');

        /* $html = '<pre style="
        font-family: Times New Roman 
        white-space: pre-wrap;
        white-space: -moz-pre-wrap;
        white-space: -pre-wrap;
        white-space: -o-pre-wrap;
        word-wrap: break-word;">'; */

        // $html = '<pre>';

        $html = "<div style='width:1000px'>";
        $arrayItems = [];
        // $startOffset = -1;

        /* if(isset($paragraph[10])) {
            die('PPPPP');
        }else {
            die('sadasdas');
        } */

        for ($i=0; $i < $paragraph->count(); $i++) {
            // echo '+++++++++++++++++++++++++++++++<br/>';

            /* paragraph özelliklerini alma başla */
            $alignment = $paragraph[$i]->getAttribute('Alignment');
            switch ($alignment) {
                case '1':
                    $alignment = 'center';
                    break;

                case '2':
                    $alignment = 'right';
                    break;

                case '3':
                    $alignment = 'justify';
                    break;
                
                default:
                    $alignment = 'left';
                    break;
            }
            $tabset = $paragraph[$i]->getAttribute('TabSet');
            $tabset = explode(',', $tabset);
            $line = -1;

            /* var_dump('special');
            var_dump($alignment);
            var_dump($tabset);
            var_dump('special end'); */
            /* paragraph özelliklerini alma bitiş */

            /* content element başla */
            $contentEl = $paragraph[$i]->getElementsByTagName('content');
            /* var_dump($contentEl);
            var_dump($contentEl->count()); */

            for ($j=0; $j < $contentEl->count(); $j++) {
                $start = $contentEl->item($j)->getAttribute('startOffset');
                $end = $contentEl->item($j)->getAttribute('length');
                $size = $contentEl->item($j)->getAttribute('size') ?? 12;
                $bold = $contentEl->item($j)->getAttribute('bold') == true ? 'font-weight:bold;' : '';
                $content = $this->pdfReplaceSpace(mb_substr($cdata, $start, $end));

                /* if($start == 126) {
                    var_dump($content);
                    dd(mb_substr($cdata, $start, $end));
                } */

                $arrayItems[$start] = [
                    'start' => $start,
                    'end' => $end,
                    'content' => $content,
                    'size' => $size,
                    'total' => $start + $end,
                ];
            }
            /* content element bitiş */

            /* field element başla */
            $fieldEl = $paragraph[$i]->getElementsByTagName('field');
            for ($k=0; $k < $fieldEl->count(); $k++) {
                $start = $fieldEl->item($k)->getAttribute('startOffset');
                $end = $fieldEl->item($k)->getAttribute('length');
                $size = $fieldEl->item($k)->getAttribute('size') ?? 12;
                $bold = $fieldEl->item($k)->getAttribute('bold') == true ? 'font-weight:bold;' : '';
                $content = $this->pdfReplaceSpace(mb_substr($cdata, $start, $end));

                $arrayItems[$start] = [
                    'start' => $start,
                    'end' => $end,
                    'content' => $content,
                    'size' => $size,
                    'total' => $start + $end,
                ];
            }
            /* field element bitiş */

            /* space element başla */
            $spaceEl = $paragraph[$i]->getElementsByTagName('space');
            for ($l=0; $l < $spaceEl->count(); $l++) {
                // $line++;
                $start = $spaceEl->item($l)->getAttribute('startOffset');
                $end = $spaceEl->item($l)->getAttribute('length');
                $size = $spaceEl->item($l)->getAttribute('size') ?? 12;
                $bold = $spaceEl->item($l)->getAttribute('bold') == true ? 'font-weight:bold;' : '';
                $content = $this->pdfReplaceSpace(mb_substr($cdata, $start, $end));

                $arrayItems[$start] = [
                    'start' => $start,
                    'end' => $end,
                    'content' => $content,
                    'size' => $size,
                    'total' => $start + $end,
                ];
            }
            /* space element bitiş */

            /* tab element başla */
            $tabEl = $paragraph[$i]->getElementsByTagName('tab');
            for ($m=0; $m < $tabEl->count(); $m++) {
                $line++;
                $start = $tabEl->item($m)->getAttribute('startOffset');
                $end = $tabEl->item($m)->getAttribute('length');
                $size = $tabEl->item($m)->getAttribute('size') ?? 12;
                $bold = $tabEl->item($m)->getAttribute('bold') == true ? 'font-weight:bold;' : '';
                $width = explode('.', $tabset[$line]);

                /* $width[0] = 1.8 * $width[0];
                $content = '<span style="width:'.$width[0].'px;text-align:right;display: inline-block">';
                $content .= $this->pdfReplaceSpace(mb_substr($cdata, $start, $end));
                $content .= '</span>'; */
                
                $width[0] = round($width[0] / 13);
                // $content = '<span style="white-space: nowrap">';
                /* manual aralık başla */
                $width[0] = $line < 1 ? 3 : 38;
                // dd($width[0]);
                /* manual aralık bitiş */

                $content = '<span>';
                // $content .= $this->pdfReplaceSpace(mb_substr($cdata, $start, $end)).$width[0].str_repeat('_', $width[0]);
                $content .= $this->pdfReplaceSpace(mb_substr($cdata, $start, $end)).str_repeat('_', $width[0]);
                $content .= '</span>';

                // dd($content);

                $arrayItems[$start] = [
                    'start' => $start,
                    'end' => $end,
                    'content' => $content,
                    'size' => $size,
                    'total' => $start + $end,
                ];
            }
            /* tab element bitiş */

            ksort($arrayItems);

            $size = intval($size) > 0 ? intval($size) + 3 : 15;
            $bold = '';

            // dd($arrayItems);
            $html .= "<div style='font-size:".$size.";margin:15px 0 15px 0; text-align:".$alignment.";".$bold."'>";
            foreach ($arrayItems as $itemKey => $itemVal) {
                /* var_dump($itemVal['start']);echo '<br>';
                var_dump($itemVal['content']);echo '<br>';
                var_dump('---------');echo '<br>'; */

                /* if($itemVal['start'] == 126) {
                    var_dump($itemVal['content']);
                    dd($itemVal['content']);
                } */
                
                $html .= $itemVal['content'];
            }
            $html .= "</div>";

            $arrayItems = [];
        }

        // $html .= "</div></pre>";
        $html .= "</div>";
        
        /* echo $html;
        echo '<br></br><br></br><br></br><br></br><br></br>'; */
        // dd($html);

        return $html;
    }
}
