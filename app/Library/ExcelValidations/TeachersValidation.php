<?php

namespace App\Library\ExcelValidations;

use App\Models\Admin\Institutions;
use Illuminate\Support\Facades\DB;

/**
 * Office File Chunk Proccess
 */
class TeachersValidation 
{
    private $institutions = Array();
    private $institutionNames = Array();
    private $provinces = Array();
    private $towns = Array();
    private $prvId = 0;

    public function __construct() {
        $this->institutions = Institutions::all()->toArray();
        $this->institutionNames = array_column($this->institutions, 'inst_name');

        $this->institutionNames = array_map(function($item) {
            return \Transliterator::create('tr-lower')->transliterate($item);
        }, $this->institutionNames);

        $provincesTbl = DB::table('provinces')->get();
        foreach ($provincesTbl as $prvKey => $prvVal) {
            $this->provinces[
                \Transliterator::create('tr-lower')->transliterate($prvVal->prv_name)
            ] = \Transliterator::create('tr-lower')->transliterate($prvVal->id);
        }

        $townsTbl = DB::table('towns')->get();
        foreach ($townsTbl as $twnKey => $twnVal) {
            $this->towns[
                \Transliterator::create('tr-lower')->transliterate($twnVal->twn_name)
                .'_'.$twnVal->prv_id
            ] = \Transliterator::create('tr-lower')->transliterate($twnVal->id);
        }
    }

    private function tckimlik($tckimlik){
        $olmaz=array('11111111110','22222222220','33333333330','44444444440','55555555550','66666666660','7777777770','88888888880','99999999990'); 
        
        $ilkt = 0;
        $sont = 0;
        $tumt = 0;

        if($tckimlik[0]==0 or strlen($tckimlik)!=11){ return false;  } 
        else{
            for($a=0;$a<9;$a=$a+2){ $ilkt=$ilkt+$tckimlik[$a]; } 
            for($a=1;$a<9;$a=$a+2){ $sont=$sont+$tckimlik[$a]; } 
            for($a=0;$a<10;$a=$a+1){ $tumt=$tumt+$tckimlik[$a]; } 
            if(($ilkt*7-$sont)%10!=$tckimlik[9] or $tumt%10!=$tckimlik[10]){ return false; } 
            else{  
                foreach($olmaz as $olurmu){ if($tckimlik==$olurmu){ return false; } } 
                return true;
            } 
        } 
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
// echo '<pre>';
        $val = null;
        switch ($name) {
            case 'thr_tc_no':
                // var_dump($value);
                /* if(strlen($value) == 11) {
                    $val = $value;
                } */
                if($this->tckimlik(strval($value))) {
                    $val = $value;
                }
                break;
            case 'prv_id':
                $val = \Transliterator::create('tr-lower')->transliterate($value);
                if(!empty($this->provinces[$val])) {
                    $val = $this->provinces[$val];
                    $this->prvId = $val;
                }else {
                    $val = null;
                }
                break;
            case 'twn_id':
                $val = \Transliterator::create('tr-lower')->transliterate($value);
                /* echo '<hr/>';
                echo '<pre>';
                var_dump($this->prvId);
                var_dump($val); */
                if(!empty($this->towns[$val.'_'.$this->prvId])) {
                    $val = $this->towns[$val.'_'.$this->prvId];
                    // var_dump($val);
                }else {
                    $val = null;
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
                // var_dump($value);
                if (preg_match("/^([1-9]|0[1-9]|[1-2][0-9]|3[0-1])[.|\/]([1-9]|0[1-9]|1[0-2])[.|\/][0-9]{4}$/", $value)) {
                    // var_dump($value);
                    $val = strtotime(str_replace('/', '-', $value));
                }else if (is_numeric($value)) {
                    // var_dump($value);
                    $UNIX_DATE = ($value - 25569) * 86400;
                    $val = gmdate("d-m-Y", $UNIX_DATE);
                    $val = strtotime(str_replace('/', '-', $val));
                }
                /* var_dump($val);
                echo '<hr>'; */
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