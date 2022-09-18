<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Library\FileUpload;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Admin\DcFiles;
use App\Models\Admin\DcLists;
use App\Models\Admin\DcComment;
use App\Models\Admin\DcDocuments;
use App\Models\Admin\DcAttachFiles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\DocumentManagement\StoreDcDocumentsRequest;
use App\Http\Requests\Admin\DocumentManagement\StoreManualDcDocumentsRequest;
use setasign\Fpdi\Fpdi;

class DocumentsController extends Controller
{
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

            $arr['content'] = $pdf->getText();
		}

		return $arr;
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

        $this->sameDocumentControl($params);
        
        $arr = [
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
            'dc_com_text'       => $params['dc_com_text'],
            'dc_manuel'         => $params['dc_manuel'],
        ];

        $dcDocuments = $this->saveDcDocument(
            $arr, 
            [
                $request->file('dc_sender_file')
            ], 
            $request->file('dc_sender_attach_files'),
        );

        /* Save Relative Document */
        $this->manualSaveRelDocument($dcDocuments, $params, $request);
        

        $msg = ['succeed' => __('messages.edit_success')];
        
        return redirect()->route('admin.document_mng.document.create')
                        ->with($msg);
    }

    private function manualSaveRelDocument($dcDocuments, $params, $request)
    {
        if (isset($params['rel_dc_number'])) {
            
            foreach ($params['rel_dc_number'] as $key => $val) {

                $arr = [
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
        $rel_dc_number = [];
            
        if(isset($params['rel_dc_number'])) {
            foreach ($params['rel_dc_number'] as $key => $val) {
                
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
        $dcComText = $params['dc_com_text'] ?? '';

        unset($params['list_id']);
        unset($params['dc_com_text']);
        

        if(empty($dcDocuments)) {
            
            $dcDocuments = DcDocuments::where([
                ['dc_number', $params['dc_number']],
                ['dc_main_status', "1"],
            ])->first();
            
            if(!empty($dcDocuments)) {
                throw ValidationException::withMessages(
                    ['document' => 'Yüklenmeye çalışılan evrak zaten mevcuttur.']
                );
            }

            $dcDocuments = DcDocuments::where(
                ['dc_number'    => $params['dc_number']],
            )->first();

            $exist = empty($dcDocuments) ? false : true;
            
            if ($exist === false) {
                $dcDocuments = DcDocuments::create(
                    // ['dc_number' => array_shift($params)],
                    $params
                );
            }else {
                $dcDocuments->dc_main_status = "1";
                $dcDocuments->save();
            }

            /* Listeye ekleme */
            if($listId > 0) {
                $dcList = DcLists::find($listId);

                $dcDocuments->dc_lists()->save($dcList);
            }

            /* İzinli kullanıcıları ekleme */

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
            )->first();

            $exist = empty($dcRelative) ? false : true;

            if($exist === false) {
                $dcRelative = DcDocuments::create(
                    // ['dc_number' => array_shift($params)],
                    $params
                );
            }

            $dcDocuments->dc_ralatives()->save($dcRelative);

            $this->uploadFile([
                'dcDocuments'   => $dcRelative,
                'params'        => $params,
                'dcFile'        => $dcFile,
                'dcAttachFiles' => $dcAttachFiles,
                'exist'         => $exist,
            ]);
        }

        return $dcDocuments;
    }
    
    public function uploadFile($arr)
    {
        extract($arr);

        if($exist === false) {
            $filesArr = $this->saveFileToStorage(
                $dcFile, 
                'DcFiles', 
                'dc_file_path',
                // 'udf'
            );
        }
        
        if(isset($dcAttachFiles)) {

            $dcAttachFilesCollection = $dcDocuments->dcAttachFiles();
            $this->deleteImageFromStorage($dcAttachFilesCollection->get());
            
            $dcAttachFilesCollection->delete();

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

        }, $fileUpload->getSavePath());

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

        try {
            $result = file_get_contents("zip://{$file->getPathName()}#content.xml");

            $pattern = '/<!\[CDATA\[\¸(.*)\n{2,10}sayı/si';
            preg_match($pattern, $result, $sender);

            // $pattern = '/konu\s*:.*([A-ZİĞÜŞÖÇ ]{10,1000}\n{2,10})/si';
            $pattern = '/konu\s*?:(.*?)\n{2,10}([A-ZİĞÜŞÖÇ \t\.\-\/]{3,1000}\n*\D*)\n{2,10}(.+)]]>/si';
            preg_match($pattern, $result, $receiver);
            
            $pattern = '/<!\[CDATA\[\¸(.*)]]>/si';
            preg_match($pattern, $result, $content);

            $pattern = '/sayı\s*?:(.*-)(\d*)\s([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})\n/si';
            preg_match($pattern, $result, $number);
            
            $receiver[3] = preg_replace('/\n/', '<br/>', $receiver[3]);
            $receiver[3] = preg_replace('/\t{3,100}/', '<span class="mr-5"></span>', $receiver[3]);
            $receiver[3] = preg_replace('/\t/', '<span class="mr-5"></span>', $receiver[3]);

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
                    ['senderFile' => 'Dosya formatı hatalı']
                );
            }

        } catch (\Throwable $th) {

            throw ValidationException::withMessages(
                ['senderFile' => 'Dosya formatı hatalı']
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
            'sender' => $sender[1],
            'subjectNumber' => $number[1],
            'number' => $number[2],
            'date' => $number[3],
            'subject' => $receiver[1],
            'content' => $content[1],
            'rawContent' => $result,
            'receiver' => $receiver[2],
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
}
