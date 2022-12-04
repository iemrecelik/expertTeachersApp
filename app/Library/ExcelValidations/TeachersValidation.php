<?php

namespace App\Library\ExcelValidations;

use App\Models\Admin\Institutions;


/**
 * Office File Chunk Proccess
 */
class TeachersValidation 
{
    private $institutions = Array();
    private $institutionNames = Array();

    public function __construct() {
        $this->institutions = Institutions::all()->toArray();
        $this->institutionNames = array_column($this->institutions, 'inst_name');

        $this->institutionNames = array_map(function($item) {
            return \Transliterator::create('tr-lower')->transliterate($item);
        }, $this->institutionNames);
    }

    private function filter($enter)
    {
        $enter['gender'] = array_map(function($gender) {
            return \Transliterator::create('tr-lower')->transliterate($gender);
        }, $enter['gender']);
        
        $enter['career'] = array_map(function($career) {
            return \Transliterator::create('tr-lower')->transliterate($career);
        }, $enter['career']);

        return $enter;
    }
    
    public function validateExcelField($name, $value, $enter)
    {
        $enter = $this->filter($enter);
        
        $val = null;
        switch ($name) {
            case 'thr_tc_no':
                if(strlen($value) == 11) {
                    $val = $value;
                }
                break;
            case 'thr_name':
                $val = \Transliterator::create('tr-title')->transliterate($value);
                break;
            case 'thr_surname':
                $val = \Transliterator::create('tr-upper')->transliterate($value);
                break;
            case 'thr_gender':
                $val = array_search(
                    \Transliterator::create('tr-lower')->transliterate($value), 
                    $enter['gender']
                    // ['erkek', 'bayan']
                );
                $val = $val !== false ? strval($val) : null;
                break;
            case 'thr_career_ladder':
                $val = array_search(
                    \Transliterator::create('tr-lower')->transliterate($value), 
                    $enter['career']
                    // ['bilinmiyor', 'öğretmen', 'uzman öğretmen', 'başöğretmen']
                );
                $val = ($val - 1);
                $val = $val !== false ? strval($val) : null;
                break;
            case 'inst_id':
                $val = array_search(
                    \Transliterator::create('tr-lower')->transliterate($value), 
                    $this->institutionNames
                );
                $val = $val !== false ? $this->institutions[$val]['id'] : null;
                break;
            case 'thr_birth_day':
                if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])[.|\/](0[1-9]|1[0-2])[.|\/][0-9]{4}$/", $value)) {
                    $val = strtotime($value);
                }
                break;
            default:
                if(in_array($name, [
                    'thr_name', 'thr_surname', 'thr_province', 
                    'thr_town', 'thr_email', 'thr_degree', 
                    'thr_task', 'thr_education', 'thr_mobile_no', 
                    'thr_place_of_task', 'thr_birth_day'
                ])) {
                    $val = $value;
                }
                break;
        }

        return $val;
    }
}