<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


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

    public function index()
    {
        $documents = \Illuminate\Support\Facades\DB::table('dc_documents as t0')->selectRaw('
            t0.id, t0.dc_number, t0.dc_subject, 
            t0.dc_date,  t2.dc_cat_name,
            t3.dc_file_path
        ')
        ->join('dc_cat as t1', 't1.dc_id', '=', 't0.id')
        ->join('dc_category as t2', 't2.id', '=', 't1.cat_id')
        ->join('dc_files as t3', 't3.dc_file_owner_id', '=', 't0.id')
        ->orderByRaw('t0.dc_date ASC')
        ->get()
        ->toArray();

        $archives = [];
        foreach ($documents as $dcKey => $dcVal) {

            if( array_key_exists($dcVal->id, $archives) ) {
                $archives[$dcVal->id]['dc_cat_name'][] = $dcVal->dc_cat_name;
            }else {
                $attFiles = \Illuminate\Support\Facades\DB::table('dc_attach_files as t0')
                    ->select('dc_att_file_path')
                    ->where('t0.dc_att_file_owner_id', 1)
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

        dd($archives);


        $exist = \App\Models\Admin\DcDocuments::where([
            ['dc_date', '=', strtotime('23-02-2023')],
            ['dc_number', '=', '69632299']
        ])->count();

        dd($exist);


        $old = ['ý', 'Ý', 'þ', 'Þ', 'ð', 'Ð'];
        $new = ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ'];

        $path = '2023/02/22/13/63f5ef7243f5bdocument.pdf';
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

        $new_message = preg_replace('/\n/', '', $new_message);
        $new_message = preg_replace('/\t/', '', $new_message);

        $new_message = preg_replace("/\s+/", " ", $new_message);
        $new_message = trim($new_message);

        dd($new_message);

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
