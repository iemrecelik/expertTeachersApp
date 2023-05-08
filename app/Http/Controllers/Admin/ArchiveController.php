<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Library\LogInfo;
use App\Models\Admin\DcArchives;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;


class ArchiveController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:show module archive'])->only('index');
        $this->middleware(['permission:create archive'])->only('recordArchive');
        $this->middleware(['permission:delete archive'])->only('destroy');
    }

    private function generateArchiveNewPath($oldPath)
    {
        $uniqPath = uniqid();
        $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
        $uniqPath .= '.'.$ext;

        return $uniqPath;
    }

    private function lawsuitAndAtt($lawsuits, $lawsuitsAtt)
    {
        $lawsuits = array_merge($lawsuits, $lawsuitsAtt);
        
        $law_dc_number = array_column($lawsuits, 'law_dc_number');
        array_multisort($law_dc_number, SORT_ASC, $lawsuits);

        return $lawsuits;
    }

    private function lawsuitsList()
    {
        $lawsuits = \Illuminate\Support\Facades\DB::table('lawsuits as t0')->selectRaw("
            /* ROW_NUMBER() OVER (
                ORDER BY t0.id ASC) line, */
            @dc_cat_name:=(
                SELECT 
                    GROUP_CONCAT(t5.dc_cat_name) 
                FROM 
                    dc_cat AS t4
                INNER JOIN 
                    dc_category AS t5 ON t5.id = t4.cat_id
                WHERE t4.dc_id = t3.id
            ) AS dc_cat_name,
            /* @dc_cat_name AS dc_cat_name, */
            t3.dc_number as law_dc_number,
            t1.thr_tc_no, 
            CONCAT(t1.thr_name, ' ', t1.thr_surname) AS thr_name_surname, 
            t2.uns_name, t3.dc_base_number, t0.law_brief, 
            t3.dc_number, t3.dc_subject, 
            IF(t3.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
            CONCAT(
                @dc_cat_name,
                '-',
                DATE_SUB(
                    DATE_FORMAT(
                        FROM_UNIXTIME(t3.dc_date), 
                        '%Y-%m-%d'
                    ),
                    INTERVAL 0 DAY
                ),
                '-', t3.dc_number
            ) AS 'archive_name'
        ")
        ->leftJoin('teachers as t1', 't1.id', '=', 't0.thr_id')
        ->leftJoin('unions as t2', 't2.id', '=', 't0.uns_id')
        ->join('dc_documents AS t3', 't3.id', '=', 't0.dc_id')
        ->orderBy('t0.id', 'asc')
        ->get();

        $lawsuits = array_map(function($lawsuit){
            return json_decode(json_encode($lawsuit), true);
        }, $lawsuits->toArray());

        return $lawsuits;
    }

    private function lawsuitsAtt()
    {
        $lawsuitsAtt = \Illuminate\Support\Facades\DB::table('lawsuits as t0')
            ->selectRaw("
                /* ROW_NUMBER() OVER (
                    ORDER BY t0.id ASC) line, */
                @dc_cat_name:= (
                    SELECT 
                        GROUP_CONCAT(t5.dc_cat_name) 
                    FROM 
                        dc_cat AS t4
                    INNER JOIN 
                        dc_category AS t5 ON t5.id = t4.cat_id
                    WHERE t4.dc_id = t3.id
                ) AS dc_cat_name,
                /* @dc_cat_name AS dc_cat_name, */
                (
                    SELECT dc_number FROM dc_documents WHERE id = t0.dc_id
                ) as law_dc_number,
                t1.thr_tc_no, 
                CONCAT(t1.thr_name, ' ', t1.thr_surname) AS thr_name_surname, 
                t2.uns_name, t3.dc_base_number, t0.law_brief, 
                t3.dc_number, t3.dc_subject, 
                IF(t3.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
                CONCAT(
                    @dc_cat_name,
                    '-',
                    DATE_SUB(
                        DATE_FORMAT(
                            FROM_UNIXTIME(t3.dc_date), 
                            '%Y-%m-%d'
                        ),
                        INTERVAL 0 DAY
                    ),
                    '-', t3.dc_number
                ) AS 'archive_name'
            ")
            ->leftJoin('teachers as t1', 't1.id', '=', 't0.thr_id')
            ->leftJoin('unions as t2', 't2.id', '=', 't0.uns_id')
            ->join('law_dc AS t4', 't4.law_id', '=', 't0.id')
            ->join('dc_documents AS t3', 't3.id', '=', 't4.dc_id')
            ->orderBy('t0.id', 'asc')
            ->get();

        $lawsuitsAtt = array_map(function($lawsuit){
            return json_decode(json_encode($lawsuit), true);
        }, $lawsuitsAtt->toArray());

        return $lawsuitsAtt;
    }

    public function lawsuitsExcel()
    {
        $lawsuits = $this->lawsuitsList();
        $lawsuitsAtt = $this->lawsuitsAtt();

        $lawsuitAndAtt = $this->lawsuitAndAtt($lawsuits, $lawsuitsAtt);

        array_unshift(
            $lawsuitAndAtt,
            [
                'Kategori İsmi',
                'Huk. Evrak Sayısı',
                'Tc No',
                'İsim Soyisim',
                'Sendika',
                'Esas No',
                'Dava Konusu',
                'Evrak Sayısı',
                'Evrak Konusu',
                'Evrak Durumu',
                'Arşiv Yolu',
            ]
        );

        return $lawsuitAndAtt;

        // $this->excelExports($lawsuitAndAtt, 'lawsuitAndAtt.xlsx');
    }

    public function documentBelongToTeacher()
    {
        $documentBelongToTeacher = \Illuminate\Support\Facades\DB::table('dc_thr as t0')
            ->selectRaw("
                ROW_NUMBER() OVER (
                    ORDER BY t1.thr_tc_no ASC) line,
                @dc_cat_name:= (
                    SELECT 
                        GROUP_CONCAT(t5.dc_cat_name) 
                    FROM 
                        dc_cat AS t4
                    INNER JOIN 
                        dc_category AS t5 ON t5.id = t4.cat_id
                    WHERE t4.dc_id = t2.id
                ) AS dc_cat_name,
                /* @dc_cat_name AS dc_cat_name, */
                t1.thr_tc_no, 
                CONCAT(t1.thr_name, ' ', t1.thr_surname) AS thr_name_surname, 
                t2.dc_number, t2.dc_subject, 
                IF(t2.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
                CONCAT(
                    @dc_cat_name,
                    '-',
                    DATE_SUB(
                        DATE_FORMAT(
                            FROM_UNIXTIME(t2.dc_date), 
                            '%Y-%m-%d'
                        ),
                        INTERVAL 0 DAY
                    ),
                    '-', t2.dc_number
                ) AS 'archive_name'
            ")
            ->join('teachers AS t1', 't1.id', '=', 't0.thr_id')
            ->join('dc_documents AS t2', 't2.id', '=', 't0.dc_id')
            ->orderBy('t1.thr_tc_no', 'asc')
            ->get();


        $documentBelongToTeacher = array_map(function($item){
            return json_decode(json_encode($item), true);
        }, $documentBelongToTeacher->toArray());

        array_unshift(
            $documentBelongToTeacher,
            [
                'Satır',
                'Kategoriler',
                'Tc No',
                'İsim Soyisim',
                'Evrak Sayısı',
                'Evrak Konusu',
                'Evrak Durumu',
                'Arşiv Yolu',
            ]
        );

        // $this->excelExports($documentBelongToTeacher, 'documentBelongToTeacher.xlsx');

        return $documentBelongToTeacher;
    }

    public function documents()
    {
        $documents = \Illuminate\Support\Facades\DB::table('dc_documents as t0')
            ->selectRaw("
                ROW_NUMBER() OVER (
                    ORDER BY t0.dc_number ASC) line, 
                @dc_cat_name:= (
                    SELECT 
                        GROUP_CONCAT(t5.dc_cat_name) 
                    FROM 
                        dc_cat AS t4
                    INNER JOIN 
                        dc_category AS t5 ON t5.id = t4.cat_id
                    WHERE t4.dc_id = t0.id
                ) AS dc_cat_name,
                /* @dc_cat_name AS dc_cat_name, */
                t0.dc_number, t0.dc_subject, 
                IF(t0.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
                CONCAT(
                    @dc_cat_name,
                    '-',
                    DATE_SUB(
                        DATE_FORMAT(
                            FROM_UNIXTIME(t0.dc_date), 
                            '%Y-%m-%d'
                        ),
                        INTERVAL 0 DAY
                    ),
                    '-', t0.dc_number
                ) AS 'archive_name'
            ")
            ->orderBy('t0.dc_number', 'asc')
            ->get();
        
        $documents = array_map(function($item){
            return json_decode(json_encode($item), true);
        }, $documents->toArray());

        array_unshift(
            $documents,
            [
                'Satır',
                'Kategori İsmi',
                'Evrak Sayısı',
                'Evrak Konusu',
                'Evrak Durumu',
                'Arşiv Yolu',
            ]
        );

        // $this->excelExports($documents, 'documents.xlsx');

        return $documents;
    }

    /* private function getExcelLetter($arrayData)
    {
        $letters = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 
            'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 
            'U', 'V', 'W', 'X', 'Y', 'Z'
        ];
        return [
            'letter' => $letters[count($arrayData[0])],
            'count' => count($arrayData),
        ];
    } */

    private function saveExcel($arrayData, $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->fromArray($arrayData, NULL,'A1');

        /* header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 */

        
        /* tablo oluşturma başla */
        // Create Table
        /* $letterRange = $this->getExcelLetter($arrayData);
        $tableRange = 'A1:'.$letterRange['letter'].$letterRange['count'];
        $table = new Table($tableRange, 'tablo_listesi'); */

        // Create Columns
        /* $table->getColumn('D')->setShowFilterButton(false);
        $table->getAutoFilter()->getColumn('A')
            ->setFilterType(AutoFilter\Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
            ->createRule()
            ->setRule(AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 2011)
            ->setRuleType(AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER); */

        // Create Table Style
        /* $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
        $tableStyle->setShowRowStripes(true);
        $tableStyle->setShowColumnStripes(true);
        $tableStyle->setShowFirstColumn(true);
        $tableStyle->setShowLastColumn(true);
        $table->setStyle($tableStyle);
        $spreadsheet->getActiveSheet()->addTable($table); */
        /* tablo oluşturma bitiş */
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $path = storage_path('app\archives\\'.$fileName);
        $writer->save($path);
        /* $writer->save('php://output');
        exit; */
    }

    public function zipArhive(bool $command = false)
    {
        $this->archiveFiles();

        $lawsuits = $this->lawsuitsExcel();
        $documentBelongToTeacher = $this->documentBelongToTeacher();
        $documents = $this->documents();

        /* dd([
            $lawsuits,
            $documentBelongToTeacher,
            $documents
        ]); */

        $this->saveExcel($lawsuits, 'davalar listesi.xlsx');
        $this->saveExcel($documentBelongToTeacher, 'öğretmenlere ait evraklar listesi.xlsx');
        $this->saveExcel($documents, 'evrak listesi.xlsx');

        $zipFilePath = $this->addZip($command);

        return $zipFilePath;
    }
    
    private function addZip($command)
    {
        $zipArchive = new \ZipArchive();
        
        $zipFilePath = storage_path('app\public\upload\zip_archives\archive_'.date('d_m_Y H_i_sa').'.zip');
        
        $status = $zipArchive->open($zipFilePath,  \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $rootPath = storage_path('app\archives');

        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zipArchive->addFile($filePath, $relativePath);
            }
        }

        $zipArchive->close();

        if($command != false) {
            $logInfo = new LogInfo('Arşiv Yöentimi');
            $zipFilePath = pathinfo($zipFilePath, PATHINFO_BASENAME);
            $logInfo->crShowLog(
                "Kaydetme::Arşiv Yönetimi::<b>{$zipFilePath}</b> dosyası kaydedildi."
            );
        }

        return $zipFilePath;
    }

    public function archiveFiles()
    {
        $documents = \Illuminate\Support\Facades\DB::table('dc_documents as t0')->selectRaw('
            t0.id, t0.dc_number, t0.dc_subject, 
            t0.dc_date,  t2.dc_cat_name,
            t3.dc_file_path
        ')
        ->join('dc_cat as t1', 't1.dc_id', '=', 't0.id')
        ->join('dc_category as t2', 't2.id', '=', 't1.cat_id')
        ->join('dc_files as t3', 't3.dc_file_owner_id', '=', 't0.id')
        ->whereRaw('
                NOT EXISTS 
                ( 
                    SELECT 1
                    FROM dc_archive AS t4
                    WHERE t4.dc_arc_number = t0.dc_number
                    AND t4.dc_arc_date = t0.dc_date
                )
            ')
        
        ->orderByRaw('t0.dc_date ASC')
        // ->toSql();
        ->get()
        ->toArray();

        $archives = [];
        foreach ($documents as $dcKey => $dcVal) {

            if( array_key_exists($dcVal->id, $archives) ) {
                $archives[$dcVal->id]['dc_cat_name'][] = $dcVal->dc_cat_name;
            }else {
                $attFiles = \Illuminate\Support\Facades\DB::table('dc_attach_files as t0')
                    ->select('dc_att_file_path')
                    ->where('t0.dc_att_file_owner_id', $dcVal->id)
                    ->get()
                    ->toArray();
                
                $attFiles = array_column($attFiles, 'dc_att_file_path');
                
                $archives[$dcVal->id] = [
                    'dc_number' => $dcVal->dc_number,
                    'dc_subject' => $dcVal->dc_subject,
                    'dc_date' => $dcVal->dc_date,
                    'dc_cat_name' => [$dcVal->dc_cat_name],
                    'dc_file_path' => $dcVal->dc_file_path,
                    'dc_att_file_path' => $attFiles,
                ];
            }
        }
        
        foreach ($archives as $arcVal) {
            $oldPath = str_replace('/', '\\', $arcVal['dc_file_path']);
            $oldPath = storage_path('app\public\upload\images\raw'.$oldPath);

            $date = date('Y-m-d', $arcVal['dc_date']);
            $number = $arcVal['dc_number'];

            foreach ($arcVal['dc_cat_name'] as $catName) {
                $uniqPath = $this->generateArchiveNewPath($oldPath);

                $newPath = "archives/$catName/$date-$number/$uniqPath";
                $directory = pathinfo($newPath, PATHINFO_DIRNAME);
                Storage::makeDirectory($directory);

                $newPath = storage_path('app/'.$newPath);
                $newPath = str_replace('/', '\\', $newPath);


                if (file_exists($oldPath)) {
                    copy($oldPath, $newPath);
                } else {
                    $logInfo = new \App\Library\LogInfo();

                    $logInfo->crShowLog(
                        $arcVal['dc_number'].' sayılı evrağın '.$oldPath.' yazısı eklenemedi.', 
                        'logs/archive.log'
                    );
                }

                foreach ($arcVal['dc_att_file_path'] as $attFileVal) {
                    $attOldPath = str_replace('/', '\\', $attFileVal);
                    $attOldPath = storage_path('app\public\upload\images\raw\\'.$attOldPath);

                    $attUniqPath = $this->generateArchiveNewPath($attOldPath);
                    $attNewPath = "archives/$catName/$date-$number/Ekler/$attUniqPath";
                    
                    $directory = pathinfo($attNewPath, PATHINFO_DIRNAME);
                    Storage::makeDirectory($directory);
                    
                    $attNewPath = storage_path('app/'.$attNewPath);
                    $attNewPath = str_replace('/', '\\', $attNewPath);

                    if (file_exists($attOldPath)) {
                        copy($attOldPath, $attNewPath);
                    } else {
                        $logInfo = new \App\Library\LogInfo();

                        $logInfo->crShowLog(
                            $arcVal['dc_number'].' sayılı evrağın ekteki '.$attOldPath.' dosyası eklenemedi.', 
                            'logs/archive.log'
                        );
                    }
                }
            }

            DcArchives::create([
                'dc_arc_number' => $arcVal['dc_number'],
                'dc_arc_date' => $arcVal['dc_date'],
            ]);
        }

        $users = User::all();
        
        return view(
            'admin.logs.index', 
            [
                'datas' => [
                    'users' => $users
                ]
            ]
        );
    }

    public function index()
    {
        // $this->zipArhive();
        $archive = [];
        $rootPath = storage_path('app\public\upload\zip_archives');

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $archive[] = [
                    'archive_name' => $relativePath,
                    'download_href' => $relativePath
                ];
            }
        }

        return view('admin.archive.index', [
            'datas' => [
                'archive' => $archive
            ]
        ]);
    }

    public function destroy($archiveName)
    {
        $path = storage_path('app\public\upload\zip_archives\\');
        $path .= "$archiveName";

        unlink($path);

        $res = true;

        $logInfo = new LogInfo('Arşiv Modülü');
        $logInfo->crDestroyLog($archiveName);

        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }

    public function recordArchive()
    {
        $res['zipFilePath'] = $this->zipArhive();
        $res['zipFilePath'] = pathinfo($res['zipFilePath'], PATHINFO_BASENAME);
        $res['succeed'] =  __('delete_success');

        return $res;
    }
}
