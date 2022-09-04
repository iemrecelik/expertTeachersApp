<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\DcDocuments;
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

        return [
			'category' => $category,
			'list' => $list,
		];
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
        
        if(isset($req['startData'])) {
            array_unshift($categoryList, $req['startData']);
        }else {
            array_unshift($categoryList, [
                'id' => 0,
                'label' => 'Üst Kategori Yok'
            ]);
        }

        return $categoryList;
    }

	private function getTreeviewCat($id = null, $whereNotId = null) 
    {
        $arr = [];
        $co = 0;
        
        $cats = DcCategory::where('dc_cat_id', $id)
                ->where('id', '!=', $whereNotId)
                ->orderBy('dc_cat_name')
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
			

	    /*Array select and search columns*/
	    /* foreach ($tblInfo['columns'] as $column) {
	        
	        if (isset($column['data']))
	            $selectCol[] = $column['data'];

	        if($column['searchable'])
	            $searchQuery[] = $column['data'];
	    } */

		

		foreach ($tblInfo['columns'] as $column) {
	        
	        if (isset($column['data'])) {
				// $selectCol .= "t1.{$column['data']}, ";
				$selectCol .= "t0.{$column['data']}, ";
			}
	    }

		
		$dcDocuments = DB::table('dc_documents as t0');

		$selectCol = substr($selectCol, 0, -2)." ";
		$dcDocuments->selectRaw($selectCol);

		$necessity = false;
		
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
						if ($data['value'] > 0) {
							$whereQuery .= "{$data['name']} = ? AND ";
							$whereQueryParams[] = $data['value'];

							$necessity = true;
						}
						break;

					case 'dc_main_status':
						if ($data['value'] > 0) {
							$whereQuery .= "{$data['name']} = '0' OR {$data['name']} = '1' AND ";
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
		
		$dcDocuments = $dcDocuments->orderBy($colOrder, $order);

        
        $recordsTotal = DcDocuments::count();
	    $recordsFiltered = $dcDocuments->count();

	    $data = $dcDocuments->offset($tblInfo['start'])
					->limit($tblInfo['length'])
					->get();

	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $data,
	        'draw' => $tblInfo['draw']
	    ];
    }

	/* public function show(DcDocuments $dcDocuments)
	{
		return $dcDocuments;
	} */
	public function show(DcDocuments $dcDocuments)
	{
		return $dcDocuments;
	}
}
