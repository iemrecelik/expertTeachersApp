<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class LogsController extends Controller
{
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

        foreach ($logFile as $line_num => $line) {
            $pattern = '/(\[.*\]) (local\.INFO:) (.*)::(.*)::(.*)/si';
            preg_match($pattern, $line, $data);

            $datas[] = $data;
        }

        // dd($datas);

        $tableDatas = [];

        foreach ($datas as $key => $val) {
            $tableDatas[] = [
                'time' => $val[1],
                'process' => $val[3],
                'moduleName' => $val[4],
            ];

            $tableDatas[] = [
                'content' => implode('<br/>', str_split($val[5], 150))
            ];
        }

        // dd($datas);
        return $tableDatas;
    }

    private function smart_wordwrap($string, $width = 75, $break = "\n") {
        // split on problem words over the line length
        $pattern = sprintf('/([^ ]{%d,})/', $width);
        $output = '';
        $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    
        foreach ($words as $word) {
            if (false !== strpos($word, ' ')) {
                // normal behaviour, rebuild the string
                $output .= $word;
            } else {
                // work out how many characters would be on the current line
                $wrapped = explode($break, wordwrap($output, $width, $break));
                $count = $width - (strlen(end($wrapped)) % $width);
    
                // fill the current line and add a break
                $output .= substr($word, 0, $count) . $break;
    
                // wrap any remaining characters from the problem word
                $output .= wordwrap(substr($word, $count), $width, $break, true);
            }
        }
    
        // wrap the final output
        return wordwrap($output, $width, $break);
    }
}
