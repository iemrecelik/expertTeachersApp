<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\DcDocuments;
use App\Models\User;
use App\Models\Admin\DcLists;
use App\Models\Admin\DcCategory;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class SearchController extends Controller
{
	public function getCategoryAndList(Request $request)
	{
		$list = $this->getList();
		$category = $this->getCategory($request);
		$users = $this->getUsers();

        return [
			'category' => $category,
			'list' => $list,
			'users' => $users,
		];
	}

	public function getUsers()
	{
		$users = User::select('id', 'name', 'email')->get();

		return $users;
	}

	public function getList($ids = null)
	{
        if(empty($ids))
            $ids = [];
        else
            $ids = $ids;

        $list = DcLists::whereNotIn('id', $ids)->get();

        return $list;
    }

	public function getCategory(Request $request)
	{
        $req = $request->all();

        if(empty($req['id']))
            $id = null;
        else
            $id = $req['id'];
            
        
        $categoryList = $this->getTreeviewCat(null, $id);

		if(empty($req['startData'])) {
			array_unshift($categoryList, [
                'id' => 0,
                'label' => 'Üst Kategori Yok'
            ]);
		}
        
        /* if(isset($req['startData'])) {
            array_unshift($categoryList, $req['startData']);
        }else {
            array_unshift($categoryList, [
                'id' => 0,
                'label' => 'Üst Kategori Yok'
            ]);
        } */

        return $categoryList;
    }

	private function getTreeviewCat($id = null, $whereNotId = null) 
    {
        $arr = [];
        $co = 0;
        
        $cats = DcCategory::where('dc_cat_id', $id)
                ->where('id', '!=', $whereNotId)
                ->orderBy('dc_order')
                ->get();

        if (!$cats->isEmpty()) {
            foreach ($cats as $key => $val) {
                $arr[$co]['id'] = $val->id;
                $arr[$co]['label'] = $val->dc_cat_name;

                $child = $this->getTreeviewCat($val->id, $whereNotId);

                if ($child) {
                    $arr[$co]['children'] = $this->getTreeviewCat($val->id, $whereNotId);
                }

                $co++;
            }
            return $arr;
        } else {
            return null;
        }
    }

    public function searchForm()
    {
        return view("admin.document_mng.search.index");
    }

    public function getSearchDocuments(Request $request)
    {
        $tblInfo = $request->all();
		$selectCol = '';
		$innerJoin = '';
		$whereQuery = '';
		$whereQueryParams = [];
		$searchQuery = '';
		$searchQueryParams = [];

		$notSelectCol = [
            'dc_cat_id',
            'user_name',
            'thr_id',
        ];

		foreach ($tblInfo['columns'] as $column) {
	        if (isset($column['data'])) {
				if(!in_array($column['data'], $notSelectCol)) {
					$selectCol .= "t0.{$column['data']}, ";
				}
			}
	    }
		
		$dcDocuments = DB::table('dc_documents as t0');

		$selectCol = substr($selectCol, 0, -2)." ";
		$dcDocuments->selectRaw($selectCol);

		$necessity = false;

		$dcCatIds = [];
		foreach ($tblInfo['datas'] as $key => $data) {
			if($data['name'] == 'dc_cat_id[]') {
				$dcCatIds[] = $data['value'];
				unset($tblInfo['datas'][$key]);
			}
		}

		array_unshift($tblInfo['datas'], [
			'name' => 'dc_cat_id',
			'value' => $dcCatIds
		]);

		// dd($tblInfo['datas']);

		foreach ($tblInfo['datas'] as $data) {

			if(isset($data['value'])) {
				switch ($data['name']) {
					case 'dc_item_status':
						if ($data['value'] > -1) {
							$whereQuery .= "{$data['name']} = ? AND ";
							$whereQueryParams[] = $data['value'];

							$necessity = true;
						}
						break;
						
					case 'dc_cat_id':
						/* if ($data['value'] > 0) {
							$whereQuery .= "{$data['name']} = ? AND ";
							$whereQueryParams[] = $data['value'];

							$necessity = true;
						} */
						if (count($data['value']) > 0) {
							// dd($data['value']);
							$dcDocuments->join('dc_cat as t6', 't6.dc_id', '=', 't0.id');
            				$dcDocuments->join('dc_category as t7', 't7.id', '=', 't6.cat_id');

							$dcDocuments->whereIn('t7.id', $data['value']);

							/* $whereQuery .= "t7.id IN ? AND ";
							$whereQueryParams[] = '('.implode(',', $data['value']).')'; */

							$necessity = true;
						}
						break;

					case 'dc_main_status':
						if ($data['value'] > 0) {
							$whereQuery .= "({$data['name']} = '0' OR {$data['name']} = '1') AND ";
							// $whereQueryParams[] = $data['value'];
						}else {
							$whereQuery .= "{$data['name']} = '1' AND ";
						}
						break;

					case 'dc_date':
						if (isset($data['value'])) {
							
							$vals = explode(" - ",$data['value']);

							$vals = [
								strtotime(str_replace('/', '-', $vals[0])),
								strtotime(str_replace('/', '-', $vals[1])),
							];

							$whereQuery .= "{$data['name']} BETWEEN ? AND ? AND ";
							$whereQueryParams = array_merge($whereQueryParams, $vals);

							$necessity = true;
						}
						break;
						
					case 'created_at':
						if (isset($data['value'])) {
							
							$vals = explode(" - ",$data['value']);

							$vals = [
								\Carbon\Carbon::parse(str_replace('/', '-', $vals[0]).' 00:00:00'),
								\Carbon\Carbon::parse(str_replace('/', '-', $vals[1]).' 23:59:59')
							];

							$whereQuery .= "t0.{$data['name']} BETWEEN ? AND ? AND ";
							$whereQueryParams = array_merge($whereQueryParams, $vals);

							$necessity = true;
						}
						break;

					case 'user_id':
						if (isset($data['value'])) {
							$dcDocuments->where('t0.user_id', $data['value']);
							$necessity = true;
						}
						break;
						
					case 'dc_list_id':
						if (!empty($data['value'])) {
							$dcDocuments->join('dc_doc_list as t1', 't1.dc_id', '=', 't0.id');
            				$dcDocuments->join('dc_lists as t2', 't2.id', '=', 't1.list_id');

							$whereQuery .= "t2.id = ? AND ";
							$whereQueryParams[] = $data['value'];
						}
						/* $innerJoin .= "INNER JOIN dc_doc_list t1 ON t1.dc_id = dc_documents.id ";
						$innerJoin .= "INNER JOIN dc_list t2 ON t2.list_id = t1.list_id "; */
						
						$necessity = true;
						
						break;
						
					case 'thr_id':
						if (!empty($data['value'])) {
							$dcDocuments->leftJoin('dc_thr as t10', 't10.dc_id', '=', 't0.id');
            				$dcDocuments->leftJoin('teachers as t11', 't11.id', '=', 't10.thr_id');
							
							// $dcDocuments->selectRaw('CONCAT(t11.thr_name, " " ,t11.thr_surname)');

							$whereQuery .= "t11.id = ? AND ";
							$whereQueryParams[] = $data['value'];
						}
						
						$necessity = true;
						
						break;
					
					default:
						$searchQuery .= "{$data['name']} LIKE ? AND ";
						$searchQueryParams[] = '%'.$data['value'].'%';

						$necessity = true;
						break;
				}
				
			}
		}

		// if(!empty($whereQuery) || !empty($searchQuery)) {
		if(!empty($necessity)) {

			if(!empty($whereQuery) || !empty($searchQuery)) {
				$whereQuery = substr($whereQuery.$searchQuery, 0, -4);

				$whereQueryParams = array_merge($whereQueryParams, $searchQueryParams);
			}
		} else {
			throw ValidationException::withMessages(
				['fillLeastOneField' => 'En az bir alanı doldurmalısınız.']
			);
		}

		/*Order settings*/
	    $colIndex = $tblInfo['order'][0]['column'];
	    $colOrder = $tblInfo['columns'][$colIndex]['data'];
	    $order = $tblInfo['order'][0]['dir'];
		
		if(!empty($whereQuery)) {
			$dcDocuments = $dcDocuments->whereRaw($whereQuery, $whereQueryParams);
		}

		$dcDocuments->leftJoin('dc_files as t3', 't3.dc_file_owner_id', '=', 't0.id');
		$dcDocuments->selectRaw('t3.dc_file_path');

		$dcDocuments->join('users as t4', 't4.id', '=', 't0.user_id');
		$dcDocuments->selectRaw('UPPER(t4.name) as user_name');
		$dcDocuments->distinct();

		/* $dcDocuments->join('dc_category as t5', 't5.id', '=', 't0.dc_cat_id');
		$dcDocuments->selectRaw('t5.dc_cat_name as dc_cat_name'); */
		
		$dcDocuments = $dcDocuments->orderBy($colOrder, $order);
		
		// dd($dcDocuments->toSql());
        
		$recordsTotal = DcDocuments::count();
	    $recordsFiltered = $dcDocuments->count();

	    $data = $dcDocuments->offset($tblInfo['start'])
					->limit($tblInfo['length'])
					->get();

		foreach ($data as $key => $val) {
			$val->dcCatNames = DB::table('dc_category as t0')
				->join('dc_cat as t1', 't1.cat_id', '=', 't0.id')
				->where('t1.dc_id', $val->id)
				->get();
		}

	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $data,
	        'draw' => $tblInfo['draw']
	    ];
    }

	public function show(DcDocuments $dcDocuments)
	{
		if($dcDocuments->dc_main_status > 0) {
			$dcDocuments->dc_ralatives;

			/* set udf file start*/
			foreach ($dcDocuments->dc_ralatives as $key => $item) {
				$dcRelFile = DB::table('dc_documents as t0')->join('dc_files as t1', 't1.dc_file_owner_id', '=', 't0.id')
				->where([
					['t0.id', $item->id]
				])
				->first();

				$dcDocuments->dc_ralatives[$key]->dc_files = $dcRelFile;
			}
			/* set udf file end*/

			/* set attach files start*/
			foreach ($dcDocuments->dc_ralatives as $key => $item) {
				$dcRelAttFile = DB::table('dc_documents as t0')
				->select('dc_att_file_path', 'dc_att_file_owner_type', 'dc_att_file_owner_id', 't1.id')
				->join('dc_attach_files as t1', 't1.dc_att_file_owner_id', '=', 't0.id')
				->where([
					['t0.id', $item->id]
				])
				->first();
				
				if(!empty($dcRelAttFile)) {
					foreach ($dcRelAttFile as $keyAttItem => $valAttItem) {
						$dcDocuments->dc_ralatives[$key]->dcAttachFiles[$keyAttItem] = $valAttItem;
					}
				}else {
					$dcDocuments->dc_ralatives[$key]->dcAttachFiles = [];
				}
			}
			/* set attach files end*/

			$dcDocuments->dcFiles;
			$dcDocuments->dcAttachFiles;
		}else {
			$relId = $dcDocuments->id;
			$mainDcDocuments = DB::table('dc_documents as t0');

			$mainDcDocuments = $mainDcDocuments->selectRaw('t0.*')
			->leftJoin('dc_relative as t1', 't1.dc_id', '=', 't0.id')
			->where([
				['t1.rel_id', $relId]
			])
			->first();
			
			/* set relative documents and udf file start*/
			$relDcDocuments = DB::table('dc_documents as t0')->leftJoin('dc_relative as t1', 't1.rel_id', '=', 't0.id')
			->where([
				['t1.dc_id', $mainDcDocuments->id]
			])
			->get();

			foreach ($relDcDocuments as $key => $item) {
				$dcRelFile = DB::table('dc_documents as t0')->leftJoin('dc_files as t1', 't1.dc_file_owner_id', '=', 't0.id')
				->where([
					['t0.id', $item->id]
				])
				->first();

				$relDcDocuments[$key]->dc_files = $dcRelFile;
			}

			$mainDcDocuments->dc_ralatives = $relDcDocuments;
			/* set relative documents and udf file end*/

			/* set documents file */
			$dcFiles = DB::table('dc_documents as t0')->leftJoin('dc_files as t1', 't1.dc_file_owner_id', '=', 't0.id')
			->where([
				['t0.id', $mainDcDocuments->id]
			])
			->get();

			$mainDcDocuments->dc_files = $dcFiles;

			$dcDocuments = $mainDcDocuments;
		}

		$belongDocuments = DB::table('dc_documents as t0')
		->select('t0.dc_number')
		->join('dc_relative as t1', 't1.dc_id', '=', 't0.id')
		->where('t1.rel_id', $dcDocuments->id)
		->get();
		
		return [
			'document' => $dcDocuments,
			'belongDocuments' => $belongDocuments
		];
	}
}
