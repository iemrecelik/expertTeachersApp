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
        // 'Â-â-î-Û-û'
        /* $old = ['ý', 'Ý', 'þ', 'Þ', 'ð', 'Ð'];
        $new = ['ı', 'İ', 'ş', 'Ş', 'ğ', 'Ğ'];

        $path = storage_path('new_doc.pdf');

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
