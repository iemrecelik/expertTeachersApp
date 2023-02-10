<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class LogsController extends Controller
{
    private function saveDocuments()
    {
        /* $mebbisBot = new \App\Library\MebBot\DysWebBot('61765236578', '1079010790');
        $result = $mebbisBot->getDocuments('11-01-2023', 'Büro Kayıt', "0"); */

        /* $mebbisBot = new \App\Library\MebBot\MebbisBot('61765236578', '1079010790');
        $result = $mebbisBot->localtest(); */
        
        // dd($result);
        $deneme = [
            [
                'name' => 'emre',
                'surname' => 'çelik'
            ]
        ];
        foreach ($result as $key => $val) {
            $documentsController = new \App\Http\Controllers\Admin\DocumentManagement\DocumentsController();
            $res = $documentsController->getBotFileInfos();     
        }

        dd($res);

        return $result;
    }

    public function index()
    {
        $this->saveDocuments();

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
