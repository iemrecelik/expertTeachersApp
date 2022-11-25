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
            if(!in_array($key, ['excel_file', 'updateDb', '_token'])) {
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

        $inputFileType = 'Xlsx';
        $url = $params['excel_file']->getPathname();

        $reader = IOFactory::createReader($inputFileType);
        
        $spreadsheet = $reader->load($url);

        $datas = $spreadsheet->getActiveSheet()->toArray();

        if ($params['updateDb']) {
            $tcnoArr = array_column($datas, $uniqueKey);
            
            $modelPathName  = $modelPath ? $modelPath."\\".$modelName : "\App\Models\Admin\\".$modelName;
            $existTeachQuery = $modelPathName::whereIn($uniqueKeyName, $tcnoArr);

            $existTeachArr = $existTeachQuery->get()->toArray();
            $existTeachTcnoArr = array_column($existTeachArr, $uniqueKeyName);

            $existTeachQuery->delete();
        }else {
            $existTeachTcnoArr = [];
        }
        
        $co = 0;
        $min = min($rowArrNumber);
        $min = intval($min);

        $insertArr = [];
        $updateArr = [];
        
        $insertErrorArr = [];
        
        $excelValidClass = "\App\Library\ExcelValidations\\".$modelName."Validation";
        $excelValidClass = new $excelValidClass();

        foreach ($datas as $key => $value) {
            $co++;

            if($min > $co) {
                continue;
            }
            $arr = [];

            $exIndex = array_search($value[$uniqueKey], $existTeachTcnoArr);

            if(empty($exIndex)) {
                foreach ($rowArrLetter as $letKey => $letVal) {
                    $val = $excelValidClass->validateExcelField($letKey, $value[$letVal]);

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
                
            }else {
                foreach ($rowArrLetter as $letKey => $letVal) {
                    $val = $excelValidClass->validateExcelField($letKey, $value[$letVal]);
                    
                    if($val === null) {
                        $insertErrorArr[] = $value[$uniqueKey];
                        $existTeachArr[$exIndex] = null;
                        break;
                    }
                    $existTeachArr[$exIndex][$letKey] = $val;
                }

                if(!empty($existTeachArr[$exIndex])) {
                    $updateArr[] = $existTeachArr[$exIndex];
                }
            }
        }

        return [
            'insertArr' => $insertArr,
            'updateArr' => $updateArr,
            'insertErrorArr' => $insertErrorArr,
        ];
    }
}