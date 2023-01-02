<?php

namespace App\Http\Controllers\Admin\OldRegulation;

use App\Http\Controllers\Controller;
use App\Models\Admin\OldRegulation\OldFirstColApp;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\OldRegulation\SearchListRequest;
use Nette\Utils\Strings;

class SearchController extends Controller
{
    public function expertInfo() 
    {
        return view('admin.old_regulation.search.index');
    }


    public function getExpertInfos(SearchListRequest $request)
    {        
        $datas = [];
        $tblInfo = $request->all();

        $tables = [
            'likeOldFirstLike' => [
                'startWord' => 'ofc_app',
                'colNames' => [
                    [ "data"=> "ofc_app_tc_num" ],
                    [ "data"=> "ofc_app_name" ],
                    [ "data"=> "ofc_app_surname" ],
                    [ "data"=> "ofc_app_min_puan" ],
                    [ "data"=> "ofc_app_main_puan"]
                ]
            ]
        ];

        foreach ($tables as $key => $val) {
            $datas[$key] = $this->getExpertInfoDatas($tblInfo, $val);
        }
        
        // dump($datas);die;

        /* $likeOldFirstLike =  $this->getRawLikeSql($tblInfo, 'ofc_app');;
        $likeOldSecondLike =  '';
        $likeAllExamsLike =  ''; */

        // $this->getRawLikeSql($tblInfo, 'ofc_app');
        

        // $info['where'] = $likeOldFirstLike;

        /* $oldFirstColAppModel = new OldFirstColApp();
        $oldFirstColAppModel->getSearchList($info); */
        /* [
            ['title'] = 'Tc NO',
            ['title'] = 'AD',
            ['title'] = 'SOYAD',
        ] */

        
        /* $datas['likeOldFirstLike']['cols'] = [
            [ "data"=> "ofc_app_tc_num" ],
            [ "data"=> "ofc_app_name" ],
            [ "data"=> "ofc_app_surname" ],
            [ "data"=> "ofc_app_min_puan" ],
            [ "data"=> "ofc_app_main_puan"]
        ];

        $datas['likeOldFirstLike']['vals'] = OldFirstColApp::getSearchList($info); */

        /* foreach ($datas['likeOldFirstLike']['val'] as $key => $val) {
            
        } */
        

        // dump($datas);

        return $datas;

        // $sql = "SELECT * FROM old_first_col_app WHERE {$likeOldFirstLike}";

        // dump($sql);die;
    }

    private function getExpertInfoDatas($tblInfo, $val)
    {
        $likeQuery =  $this->getRawLikeSql($tblInfo, $val['startWord']);
        
        $info['where'] = $likeQuery;

        $datas['cols'] = $val['colNames'];
        $datas['vals'] = OldFirstColApp::getSearchList($info);

        return $datas;
    }

    private function getRawLikeSql(Array $datas, String $tblName)
    {
        $likeQuery = '';

        foreach ($datas as $key => $val) {

            if (isset($val)) {
                $likeQueryKey = "{$tblName}_{$key}";
                $likeQuery .= $likeQuery 
                    ? " AND {$likeQueryKey} LIKE"
                    : " {$likeQueryKey} LIKE";
                $likeQuery .= " '%{$val}%'";
            }
        }

        return $likeQuery;
    }
}
