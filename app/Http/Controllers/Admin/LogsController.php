<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DcArchives;
use Illuminate\Http\Request;
use App\Models\User;


use Illuminate\Support\Facades\Storage;


class LogsController extends Controller
{
    private function saveDocuments()
    {
        // $dysBot = new \App\Library\MebBot\DysWebBot('61765236578', '1079010790');
        $dysBot = new \App\Library\MebBot\DysWebBot();
        // $result = $dysBot->getDocuments('11-01-2023', "0");
        $result = $dysBot->getDocuments('08-11-2022', "1");

        /* $mebbisBot = new \App\Library\MebBot\MebbisBot('61765236578', '1079010790');
        $result = $mebbisBot->localtest(); */
        dd($result);

        /* $existDoc = \App\Models\Admin\DcDocuments::select('dc_number')->where('dc_date', strtotime('18-11-2022'))->get()->toArray();
        $existDoc = array_column($existDoc, 'dc_number');
        dd($existDoc); */

        $result = [
            [
                "dc_item_status" => "1",
                "dc_who_send" => " ÖĞRETMEN YETİŞTİRME VE GELİŞTİRME GENEL MÜDÜRLÜĞÜ",
                "dc_number" => "63071835",
                "dc_date" => 1667854800,
                "dc_subject" => "Yılmaz GİRAY (T.C. NO:24403766462)",
                "dc_who_receiver" => "ERZURUM İL MİLLİ EĞİTİM MÜDÜRLÜĞÜ(MERKEZ / ERZURUM)",
                "dc_file_path" => "/2023/02/17/10/document.pdf",
            ],
            [
                "dc_item_status" => "1",
                "dc_who_send" => " ÖĞRETMEN YETİŞTİRME VE GELİŞTİRME GENEL MÜDÜRLÜĞÜ",
                "dc_number" => "63071794",
                "dc_date" => 1667854800,
                "dc_subject" => "Rüstem FIRAT (T.C. NO: 24352013044 )",
                "dc_who_receiver" => "ERZURUM İL MİLLİ EĞİTİM MÜDÜRLÜĞÜ(MERKEZ / ERZURUM)|ÖLÇME, DEĞERLENDİRME VE SINAV HİZMETLERİ GENEL MÜDÜRLÜĞÜ(.)",
                "dc_file_path" => "/2023/02/17/10/63ef2b518d446document.pdf",
                "dc_att_file_path" => [
                    "/2023/02/17/10/belge1676618564749.udf",
                    "/2023/02/17/10/belge1676618565985.pdf",
                ]
            ],
            [
                "dc_item_status" => "1",
                "dc_who_send" => " ÖĞRETMEN YETİŞTİRME VE GELİŞTİRME GENEL MÜDÜRLÜĞÜ",
                "dc_number" => "63071750",
                "dc_date" => 1667854800,
                "dc_subject" => "Akın AKSU (T.C. NO: 47095226892 )",
                "dc_who_receiver" => "ERZURUM İL MİLLİ EĞİTİM MÜDÜRLÜĞÜ(MERKEZ / ERZURUM)|ÖLÇME, DEĞERLENDİRME VE SINAV HİZMETLERİ GENEL MÜDÜRLÜĞÜ(.)|ÖLÇME, DEĞERLENDİRME VE SINAV HİZMETLERİ GENEL MÜDÜRLÜĞÜ(.)",
                "dc_file_path" => "/2023/02/17/10/63ef2b6d77164document.pdf",
                "dc_att_file_path" => [
                    "/2023/02/17/10/belge1676618591432.udf",
                    "/2023/02/17/10/belge1676618592762.pdf",
                    "/2023/02/17/10/belge1676618593675.pdf",
                ]
            ],
            [
                "dc_item_status" => "1",
                "dc_who_send" => " ÖĞRETMEN YETİŞTİRME VE GELİŞTİRME GENEL MÜDÜRLÜĞÜ",
                "dc_number" => "63071685",
                "dc_date" => 1667854800,
                "dc_subject" => "Türkan TİRYAKİ (T.C. NO: 46996100768 )",
                "dc_who_receiver" => "ERZURUM İL MİLLİ EĞİTİM MÜDÜRLÜĞÜ(MERKEZ / ERZURUM)|ÖLÇME, DEĞERLENDİRME VE SINAV HİZMETLERİ GENEL MÜDÜRLÜĞÜ(.)|ÖLÇME, DEĞERLENDİRME VE SINAV HİZMETLERİ GENEL MÜDÜRLÜĞÜ(.)|ÖLÇME, DEĞERLENDİRME VE SINAV HİZMETLERİ GENEL MÜDÜRLÜĞÜ(.)",
                "dc_file_path" => "/2023/02/17/10/63ef2b81d0932document.pdf",
                "dc_att_file_path" => [
                    "/2023/02/17/10/belge1676618611827.udf",
                    "/2023/02/17/10/belge1676618613109.pdf",
                    "/2023/02/17/10/belge1676618614209.pdf",
                ]
            ]
        ];

        foreach ($result as $key => $val) {
            $documentsController = new \App\Http\Controllers\Admin\DocumentManagement\DocumentsController();
            $content = $documentsController->getBotFileInfos($val['dc_file_path']);
            
            $result[$key]['dc_content'] = $content;
            /* $result[$key]['dc_show_content'] = $fileInfos['showContent'];
            $result[$key]['dc_raw_content'] = $fileInfos['rawContent']; */
        }

        return $result;
    }

    private function generateArchiveNewPath($oldPath)
    {
        $uniqPath = uniqid();
        $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
        $uniqPath .= '.'.$ext;

        return $uniqPath;
    }

    private function lawsuitList()
    {
        $lawsuits = \Illuminate\Support\Facades\DB::table('lawsuits as t0')->selectRaw("
            ROW_NUMBER() OVER (
                ORDER BY t0.id ASC) line,
            (
                SELECT 
                    GROUP_CONCAT(t5.dc_cat_name) 
                FROM 
                    dc_cat AS t4
                INNER JOIN 
                    dc_category AS t5 ON t5.id = t4.cat_id
                WHERE t4.dc_id = t3.id
            ) AS dc_cat_name,
            t1.thr_tc_no, 
            CONCAT(t1.thr_name, ' ', t1.thr_surname) AS thr_name_surname, 
            t2.uns_name, t3.dc_base_number, t0.law_brief, 
            t3.dc_number, t3.dc_subject, 
            IF(t3.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
            CONCAT(
                DATE_SUB(
                    DATE_FORMAT(
                        FROM_UNIXTIME(t3.dc_date), 
                        '%Y-%m-%d'
                    ),
                    INTERVAL 1 DAY
                ),
                '-', t3.dc_number
            ) AS 'archive_name'
        ")
        ->leftJoin('teachers as t1', 't1.id', '=', 't0.thr_id')
        ->leftJoin('unions as t2', 't2.id', '=', 't0.uns_id')
        ->join('dc_documents AS t3', 't3.id', '=', 't0.dc_id')
        ->orderBy('t0.id', 'asc')
        ->get();

        // dd($lawsuits);
        
        $lawsuitsAtt = \Illuminate\Support\Facades\DB::table('lawsuits as t0')
            ->selectRaw("
                ROW_NUMBER() OVER (
                    ORDER BY t0.id ASC) line,
                (
                    SELECT 
                        GROUP_CONCAT(t5.dc_cat_name) 
                    FROM 
                        dc_cat AS t4
                    INNER JOIN 
                        dc_category AS t5 ON t5.id = t4.cat_id
                    WHERE t4.dc_id = t3.id
                ) AS dc_cat_name,
                t1.thr_tc_no, 
                CONCAT(t1.thr_name, ' ', t1.thr_surname) AS thr_name_surname, 
                t2.uns_name, t3.dc_base_number, t0.law_brief, 
                t3.dc_number, t3.dc_subject, 
                IF(t3.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
                CONCAT(
                    DATE_SUB(
                        DATE_FORMAT(
                            FROM_UNIXTIME(t3.dc_date), 
                            '%Y-%m-%d'
                        ),
                        INTERVAL 1 DAY
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

        $documentBelongToTeacher = \Illuminate\Support\Facades\DB::table('dc_thr as t0')
            ->selectRaw("
                ROW_NUMBER() OVER (
                    ORDER BY t1.thr_tc_no ASC) line,
                t1.thr_tc_no, 
                CONCAT(t1.thr_name, ' ', t1.thr_surname) AS thr_name_surname, 
                t2.dc_number, t2.dc_subject, 
                IF(t2.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
                CONCAT(
                    DATE_SUB(
                        DATE_FORMAT(
                            FROM_UNIXTIME(t2.dc_date), 
                            '%Y-%m-%d'
                        ),
                        INTERVAL 1 DAY
                    ),
                    '-', t2.dc_number
                ) AS 'archive_name'
            ")
            ->join('teachers AS t1', 't1.id', '=', 't0.thr_id')
            ->join('dc_documents AS t2', 't2.id', '=', 't0.dc_id')
            ->orderBy('t1.thr_tc_no', 'asc')
            ->get();
        
            $documents = \Illuminate\Support\Facades\DB::table('dc_documents as t0')
                ->selectRaw("
                    ROW_NUMBER() OVER (
                        ORDER BY t0.dc_number ASC) line, 
                    t0.dc_number, t0.dc_subject, 
                    IF(t0.dc_item_status = '0', 'GELEN', 'GİDEN' ) AS dc_item_status,
                    CONCAT(
                        DATE_SUB(
                            DATE_FORMAT(
                                FROM_UNIXTIME(t0.dc_date), 
                                '%Y-%m-%d'
                            ),
                            INTERVAL 1 DAY
                        ),
                        '-', t0.dc_number
                    ) AS 'archive_name'
                ")
                ->orderBy('t0.dc_number', 'asc')
                ->get();
        dd([
            $lawsuits->toArray(),
            $lawsuitsAtt->toArray(),
            $documentBelongToTeacher->toArray(),
            $documents->toArray(),
        ]);
    }

    public function index2()
    {
        // $this->lawsuitList();
        
        // dd(Storage::path('public/upload/images/2023/02/27/16/hatay_uzman.udf'));
        /* $raw = 'public/upload/images/raw/2023/02/27/16/hatay_uzman.udf';
        var_dump(Storage::path($raw));
        $exists = Storage::exists($raw);

        dd($exists); */
        $documents = \Illuminate\Support\Facades\DB::table('dc_documents as t0')->selectRaw('
            t0.id, t0.dc_number, t0.dc_subject, 
            t0.dc_date,  t2.dc_cat_name,
            t3.dc_file_path
        ')
        ->join('dc_cat as t1', 't1.dc_id', '=', 't0.id')
        ->join('dc_category as t2', 't2.id', '=', 't1.cat_id')
        ->join('dc_files as t3', 't3.dc_file_owner_id', '=', 't0.id')
        ->where(\Illuminate\Support\Facades\DB::raw('
                NOT EXISTS 
                ( 
                    SELECT 1
                    FROM dc_archive AS t4
                    WHERE t4.dc_arc_number = t0.dc_number
                    AND t4.dc_arc_date = t0.dc_date
                )
            ')
        )
        ->orderByRaw('t0.dc_date ASC')
        // ->toSql();
        ->get()
        ->toArray();

        // dd($documents);

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
        // echo '<pre>';
        foreach ($archives as $arcVal) {
            // $oldPath = 'public/upload/images/raw/'.$arcVal['dc_file_path'];
            $oldPath = str_replace('/', '\\', $arcVal['dc_file_path']);
            $oldPath = storage_path('app\public\upload\images\raw'.$oldPath);

            /* echo '<pre>';
            var_dump($oldPath);
            var_dump(Storage::path('public\upload\images\raw\2023\04\04\12\Giden Evrak (2022_06_02__12_10_45--50931304)--801685271.udf'));
            dd(Storage::exists(
                'public\upload\images\raw\2023\04\04\12\Giden Evrak (2022_03_02__14_09_40--44791582)--758931940.udf'
            )); */


            $date = date('Y-m-d', $arcVal['dc_date']);
            $number = $arcVal['dc_number'];

            foreach ($arcVal['dc_cat_name'] as $catName) {
                // var_dump($catName);
                $uniqPath = $this->generateArchiveNewPath($oldPath);

                $newPath = "archives/$catName/$date-$number/$uniqPath";

                Storage::copy($oldPath, $newPath);

                foreach ($arcVal['dc_att_file_path'] as $attFileVal) {
                    // $attOldPath = 'public/upload/images/raw/'.$attFileVal;
                    $attOldPath = str_replace('/', '\\', $attFileVal);
                    $attOldPath = storage_path('app\public\upload\images\raw\\'.$attOldPath);

                    $attUniqPath = $this->generateArchiveNewPath($attOldPath);
                    $attNewPath = "archives/$catName/$date-$number/Ekler/$attUniqPath";
                    /* var_dump($attOldPath);
                    var_dump($attNewPath); */
                    Storage::copy($attOldPath, $attNewPath);    
                }
            }

            \App\Models\Admin\DcArchives::create([
                'dc_arc_number' => $arcVal['dc_number'],
                'dc_arc_date' => $arcVal['dc_date'],
            ]);
        }
        // die;
        dd($archives);

        /* $exist = \App\Models\Admin\DcDocuments::where([
            ['dc_date', '=', strtotime('23-02-2023')],
            ['dc_number', '=', '69632299']
        ])->count();

        dd($exist);


        $old = ['ý', 'Ý', 'þ', 'Þ', 'ð', 'Ð'];
        $new = ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ'];

        $path = '/2023/02/28/10/document.pdf';
        $path = str_replace('/', '\\', $path);
        $path = storage_path('app\public\upload\images\raw\\'.$path);

        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        $new_message = str_replace(
            $old,
            $new,
            $text
        );

        // $new_message = preg_replace('/\n/', '', $new_message);
        $new_message = preg_replace('/\t/', '', $new_message);
        // dd($new_message);
        $new_message = preg_replace("/\s+/", " ", $new_message);
        $new_message = trim($new_message);

        dd($new_message); */

        /* $mebbisBot = new \App\Library\MebBot\MebbisBot('61765236578', '1079010790');
        $result = $mebbisBot->localtest();

        dd($result); */
        /* $old = ['ý', 'Ý', 'þ', 'Þ', 'ð', 'Ð'];
        $new = ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ'];

        $path = storage_path('app\public\upload\images\raw\2023\02\21\11\63f48809c91e2document.pdf');

        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        $new_message = str_replace(
            $old,
            $new,
            $text
        );

        dd($new_message); */

        // 'Â-â-î-Û-û'
        /* $old = ['ý', 'Ý', 'þ', 'Þ', 'ð', 'Ð'];
        $new = ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ'];

        $path = storage_path('/2023/02/21/11/63f48809c91e2document.pdf');

        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        $new_message = str_replace(
            $old,
            $new,
            $text
        );

        dd($new_message);
        
        // $this->getDocumentCount();
        $arr = $this->saveDocuments();

        // dd($arr);

        foreach ($arr as $key => $val) {
            $user = auth()->user();

            $val['user_id'] = $user->id;

            $attFilePaths = $val['dc_att_file_path'] ?? [];
            $filePath = $val['dc_file_path'];
            unset($val['dc_file_path']);
            unset($val['dc_att_file_path']);

            $val['dc_show_content'] = '';
            $val['dc_raw_content'] = '';
            
            $dc = \App\Models\Admin\DcDocuments::create($val);

            $dcFiles = new \App\Models\Admin\DcFiles(
                [
                    'dc_file_path' => $filePath
                ]
            );

            $dc->dcFiles()->saveMany([$dcFiles]);

            if(count($attFilePaths) > 0 ) {
                $dcAttachFiles = array_map(function($path) {
                    return new \App\Models\Admin\DcAttachFiles([
                            'dc_att_file_path' => $path
                        ])
                    ;
                }, $attFilePaths);

                $dc->dcAttachFiles()->saveMany($dcAttachFiles);
            } 
        } */

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

    // test function
    private function betweenTimestamp()
    {
        $arr = \Illuminate\Support\Facades\DB::table('dc_documents as t0')
            ->whereBetween(
                'created_at', 
                [
                    \Carbon\Carbon::parse('2023-04-04'), 
                    \Carbon\Carbon::parse('2023-04-05')
                ])
            ->get()
            ->toArray();

        dd($arr);
    }

    public function index()
    {
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

    private function getDocumentCount()
    {
        $dysBot = new \App\Library\MebBot\DysWebBot('61765236578', '1079010790');
        $count = $dysBot->getIncomeAndSendDocumentCount('11-01-2023');

        dd($count);
    }

    public function getLogsList(Request $request)
    {
        $params = $request->all();
        // dd($params);

        $time = $params['log_date'] ?  $params['log_date'] : date('d/m/Y');

        // dd(str_replace("/", ".", $time));
        // dd($time);
        $time = strtotime(str_replace("/", ".", $time));

        $logFile = file(storage_path('logs/users/'.$time.'/'.$params['email'].'.log'));
        $logCollection = [];

        $co = 0;
        $datas = [];
        foreach ($logFile as $line_num => $line) {
            $pattern = '/(\[.*\]) (local\.INFO:) (.*)::(.*)::(.*)/si';
            preg_match($pattern, $line, $data);

            if(count($data) < 1) {
                $datas[($co-1)][5] .= $line;
            }else {
                $datas[] = $data;
                $co++;
            }
        }

        // dd($datas);

        $tableDatas = [];

        foreach ($datas as $key => $val) {
            $tableDatas[] = [
                'time' => $val[1],
                'process' => $val[3],
                'moduleName' => $val[4],
            ];

            // $val[5] = preg_replace("/\n/", "<br/>", $val[5]);
            // $val[5] = preg_replace("/\s+/", " ", $val[5]);

            $tableDatas[] = [
                // 'content' => implode('<br/>', str_split($val[5], 150))
                'content' => $val[5]
            ];
        }

        // dd($tableDatas);

        return $tableDatas;
    }
}
