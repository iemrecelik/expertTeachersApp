<?php

namespace App\Library;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Validation\ValidationException;

/**
 * Office File Chunk Proccess
 */
class ExcelProcess
{   
    public function getExcelDatas(Array $params, String $uniqueKeyName, String $modelName, String $modelPath = null)
    {
        /* Excel satır sayılarının eşitliğnin kontrolü başla */
        $rowArrLetter = [];
        $rowArrNumber = [];
        foreach ($params as $key => $val) {
            /* if(!in_array($key, ['excel_file', 'updateDb'])) {
                preg_match_all('/([0-9]+|[a-zA-Z]+)/', $val, $matches);

                $rowArrLetter[$key] = $matches[1][0];
                $rowArrNumber[] = $matches[1][1];
            } */
            if(!in_array($key, ['excel_file', 'updateDb', '_token', 'enter'])) {
                $rowArr = explode('_', $val);
                
                if($key == $uniqueKeyName) {
                    $uniqueKey = $rowArr[0];
                }
                
                $rowArrLetter[$key] = $rowArr[0];
                $rowArrNumber[] = $rowArr[1];
            }
        }

        $rowArrNumber = array_unique($rowArrNumber);

        if(count($rowArrNumber) > 1) {
            throw ValidationException::withMessages(
                ['row' => 'Bütün satır sayıları aynı olmak zorundadır.']
            );
        }else if(count($rowArrNumber) < 1) {
            throw ValidationException::withMessages(
                ['row' => 'En az bir sütun seçmelisiniz.']
            );
        }
        /* Excel satır sayılarının eşitliğnin kontrolü bitiş */
        /* $locale = 'tr';
        $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale); */

        $inputFileType = 'Xlsx';
        $url = $params['excel_file']->getPathname();

        // IOFactory::setLocale('tr');
        $reader = IOFactory::createReader($inputFileType);
        
        $spreadsheet = $reader->load($url);

        $datas = $spreadsheet->getActiveSheet()->toArray();

        /* if ($params['updateDb']) {
            $tcnoArr = array_column($datas, $uniqueKey);
            
            $modelPathName  = $modelPath ? $modelPath."\\".$modelName : "\App\Models\Admin\\".$modelName;
            $existTeachQuery = $modelPathName::whereIn($uniqueKeyName, $tcnoArr);

            $existTeachArr = $existTeachQuery->get()->toArray();
            $existTeachTcnoArr = array_column($existTeachArr, $uniqueKeyName);

            $existTeachQuery->delete();
        }else {
            $existTeachTcnoArr = [];
        } */
        
        $tcnoArr = array_column($datas, $uniqueKey);
        
        $modelPathName  = $modelPath ? $modelPath."\\".$modelName : "\App\Models\Admin\\".$modelName;
        $existTeachQuery = $modelPathName::whereIn($uniqueKeyName, $tcnoArr);

        $existTeachArr = $existTeachQuery->get()->toArray();
        $existTeachTcnoArr = array_column($existTeachArr, $uniqueKeyName);

        /* var_dump(count($existTeachTcnoArr));
        dd($existTeachTcnoArr); */

        // $existTeachQuery->delete();
        
        
        $co = 0;
        $min = min($rowArrNumber);
        $min = intval($min);

        $insertArr = [];
        $updateArr = [];
        
        $insertErrorArr = [];
        
        $excelValidClass = "\App\Library\ExcelValidations\\".$modelName."Validation";
        $excelValidClass = new $excelValidClass();
        /* echo '<pre>';
        var_dump(strtotime('01-01-1970'));
        var_dump(date('d-m-Y', -7200)); */

        // echo '<pre>';
        // var_dump($existTeachTcnoArr);
        // var_dump($datas);
        foreach ($datas as $key => $value) {
            $co++;

            if($min > $co) {
                continue;
            }
            $arr = [];

            $exIndex = array_search($value[$uniqueKey], $existTeachTcnoArr);

            /* var_dump($exIndex);
            var_dump($value[$uniqueKey]); */

            if(!is_numeric($exIndex)) {
                /* var_dump('insert');
                var_dump($value[$uniqueKey]); */
                foreach ($rowArrLetter as $letKey => $letVal) {
                    $val = $excelValidClass->validateExcelField($letKey, $value[$letVal], $params['enter']);

                    if($val === null) {
                        $insertErrorArr[] = $value[$uniqueKey];
                        $arr = null;
                        break;
                    }
                    $arr[$letKey] = $val;
                }

                if(!empty($arr)) {
                    $insertArr[] = $arr;
                }
                
            }else if (isset($params['updateDb'])) {
                /* var_dump('update');
                var_dump($value[$uniqueKey]); */
                foreach ($rowArrLetter as $letKey => $letVal) {
                    $val = $excelValidClass->validateExcelField($letKey, $value[$letVal], $params['enter']);
                    
                    if($val === null) {
                        $insertErrorArr[] = $value[$uniqueKey];
                        $existTeachArr[$exIndex] = null;
                        break;
                    }
                    $existTeachArr[$exIndex][$letKey] = $val;
                }

                unset($existTeachArr[$exIndex]['id']);
                unset($existTeachArr[$exIndex]['created_at']);
                unset($existTeachArr[$exIndex]['updated_at']);

                if(!empty($existTeachArr[$exIndex])) {
                    $updateArr[] = $existTeachArr[$exIndex];
                }
            }
        }
        /* echo '<pre>';
        var_dump($insertArr);
        dd($updateArr); */
// die;
        return [
            'insertArr' => $insertArr,
            'updateArr' => $updateArr,
            'insertErrorArr' => $insertErrorArr,
        ];
    }
}