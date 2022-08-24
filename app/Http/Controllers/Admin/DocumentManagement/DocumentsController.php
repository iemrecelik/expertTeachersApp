<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Library\FileUpload;
use Illuminate\Http\Request;
use App\Models\Admin\DcFiles;
use App\Models\Admin\DcAttachFiles;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\DocumentManagement\StoreDcDocumentsRequest;
use App\Models\Admin\DcDocuments;

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
     * Store a newly created resource in storage.
     *
     * @param  StoreDcDocumentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    public function store(StoreDcDocumentsRequest $request)
    {
        $params = $request->all();
        
        $arr = [
            'dc_item_status'    => $params['dc_item_status'],
            'dc_cat_id'         => $params['dc_cat_id'],
            'dc_number'         => $params['dc_number'],
            'dc_subject'        => $params['dc_subject'],
            'dc_who_send'       => $params['dc_who_send'],
            'dc_who_receiver'   => $params['dc_who_receiver'],
            'dc_subject'        => $params['dc_subject'],
            'dc_content'        => $params['dc_content'],
            'dc_raw_content'    => $params['dc_raw_content'],
            'dc_date'           => strtotime($params['dc_date']),
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
                    'dc_item_status'    => $params['rel_dc_item_status'][$key],
                    'dc_cat_id'         => $params['dc_cat_id'],
                    'dc_number'         => $params['rel_dc_number'][$key],
                    'dc_subject'        => $params['rel_dc_subject'][$key],
                    'dc_who_send'       => $params['rel_dc_who_send'][$key],
                    'dc_who_receiver'   => $params['rel_dc_who_receiver'][$key],
                    'dc_subject'        => $params['rel_dc_subject'][$key],
                    'dc_content'        => $params['rel_dc_content'][$key],
                    'dc_raw_content'    => $params['rel_dc_raw_content'][$key],
                    'dc_date'           => strtotime($params['rel_dc_date'][$key]),
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

    private function saveDcDocument($params, $dcFile, $dcAttachFiles = null, $dcDocuments = null)
    {
        // dump($params);die('sss');
        if(empty($dcDocuments)) {
            $dcDocuments = DcDocuments::create($params);
            $this->uploadFile([
                'dcDocuments'   => $dcDocuments,
                'params'        => $params,
                'dcFile'        => $dcFile,
                'dcAttachFiles' => $dcAttachFiles,
            ]);
        }else {
            $dcRelative = DcDocuments::create($params);
            $dcDocuments->dc_ralatives()->save($dcRelative);
            $this->uploadFile([
                'dcDocuments'   => $dcRelative,
                'params'        => $params,
                'dcFile'        => $dcFile,
                'dcAttachFiles' => $dcAttachFiles,
            ]);
        }
        // dump($params);die('sss');
        return $dcDocuments;
    }

    public function uploadFile($arr)
    {
        extract($arr);

        $filesArr = $this->saveFileToStorage(
            $dcFile, 
            'DcFiles', 
            'dc_file_path',
            'udf'
        );
        
        // dump($dcAttachFiles);die;
        if(isset($dcAttachFiles)) {
            $attachFilesArr = $this->saveFileToStorage(
                $dcAttachFiles, 
                'DcAttachFiles', 
                'dc_att_file_path'
            );
        }
            
        /* New images will be saved to databse */
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

            $pattern= '/<!\[CDATA\[\¸(.*)\n{2,10}sayı/si';
            preg_match($pattern, $result, $sender);
            
            // $pattern= '/konu\s*?:(.*?)\n{2,10}(\D{3,500})\n{2,10}/si';
            $pattern= '/konu\s*?:(.*?)\n{2,10}([A-ZİĞÜŞÖÇ ]{3,1000}\n\D*)\n{2,10}/si';
            preg_match($pattern, $result, $receiver);

            // $pattern= '/konu\s*?:.*?\n{2,10}[A-ZİĞÜŞÖÇ ]{3,1000}\n\D*\n{2,10}(.*?)\n{2,10}.*?]]>/si';
            $pattern= '/<!\[CDATA\[\¸(.*)]]>/si';
            preg_match($pattern, $result, $content);

            /* dump($result);
            echo '<pre>------sender------</pre>';
            dump($content);die; */

            $pattern= '/sayı\s*?:(.*-)(\d*)\s([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})\n/si';
            preg_match($pattern, $result, $number);

            /* echo '<pre>------sender------</pre>';
            dump($sender);
            echo '<pre>------receiver------</pre>';
            dump($receiver);;
            echo '<pre>------number------</pre>';
            dump($number);
            echo '<pre>------number------</pre>';
            dump($result);die; */

            if (
                empty($sender[1]) || empty($number[1]) || 
                empty($number[2]) || empty($number[3]) ||
                empty($receiver[1]) || empty($receiver[2])
            ) {
                throw ValidationException::withMessages(
                    ['senderFile' => 'Dosya formatı hatalı']
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
            ];

        } catch (\Throwable $th) {

            throw ValidationException::withMessages(
                ['senderFile' => 'Dosya formatı hatalı']
            );
        }

        return $arr;
    }

    /* public function getImages(Books $book)
    {
        return $book->images;
    } */

    /* public function updateImages(UpdateImagesPost $request, Books $book)
    {
        $this->loadImages($request, $book);

        return ['succeed' => __('messages.edit_success')];
    } */

    /* public function loadImages($request, Books $book)
    {
        $oldImgIDs = $request->input('altImages');

        $filters = config('imageFilters.filter.bookImagesFilt'); */

        /* New images will be saved to storage */
    /*     $imgs = $request->file('images.*.file');
        $crops = $request->input('images.*.crops');

        if($imgs)
            $imgsArr = $this->saveImageToStorage($imgs, $crops, $filters);
        else
            $imgsArr = null; */

        /* Images will be deleted */
        /* $oldImages = $book->images
                            ->whereNotIn('id', $oldImgIDs);

        if($oldImages->isNotEmpty())
            $this->deleteImageFromStorage($oldImages, $filters);
 */
        /* New images will be saved to databse */
        /* if($imgsArr)
            $book->images()->saveMany($imgsArr);
    } */

    /* private function convertToCrop($crop)
    {
        $cropFilt = [];

        foreach ($crop as $key => $val) {
            $cropFilt[$key]['crop'] = explode('*?*', $val);
        }

        return $cropFilt;
    } */

   /*  private function saveImageToStorage($images, $crops = null, $filters)
    {
        $imgFileUpload = new ImgFileUpload();
        foreach ($images as $key => $imgVal) {
            
            if (isset($crops[$key])) {
                $crop = $this->convertToCrop($crops[$key]);
                $filt = array_merge_recursive($crop, $filters);
            }else
                $filt = $filters;

            $imgFileUpload->setConfig(
                $imgVal, 
                $filt
            );
            $imgFileUpload->saveImg();
        }

        $imagesArr = array_map(function($path){

            $path = str_replace('public', 'storage', $path);
            return new Images(['img_path' => $path]);

        }, $imgFileUpload->getSavePath());

        return $imagesArr;
    }

    private function deleteImageFromStorage($oldImages, $filters)
    {
        $deleteImgs = [];
        foreach ($oldImages as $oldImage) {

            $path = $oldImage->img_path;
            $dltImgs = array_map(
                
                function($filt) use ($path){
                    return "/public/upload/images/{$filt}/{$path}";
                }, 
                array_keys($filters)
            );

            $deleteImgs = array_merge($deleteImgs, $dltImgs);
        }

        Storage::delete($deleteImgs);
        Images::destroy($oldImages->pluck('id')->all());
    } */
}
