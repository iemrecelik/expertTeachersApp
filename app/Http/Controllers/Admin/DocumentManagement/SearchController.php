<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use Illuminate\Http\Request;
use App\Models\Admin\DcDocuments;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class SearchController extends Controller
{
    public function searchForm()
    {
        return view("admin.document_mng.search.index");
    }

    public function getSearchDocuments(Request $request)
    {
        $tblInfo = $request->all();
		$selectCol = '';
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
				$selectCol .= "{$column['data']}, ";
			}
	    }

		$selectCol = substr($selectCol, 0, -2)." ";
		
		foreach ($tblInfo['datas'] as $data) {

			if(isset($data['value'])) {
				switch ($data['name']) {
					case 'dc_item_status':
						if ($data['value'] > -1) {
							$whereQuery .= "{$data['name']} = ? AND ";
							$whereQueryParams[] = $data['value'];
						}
						break;
						
					case 'dc_cat_id':
						if ($data['value'] > 0) {
							$whereQuery .= "{$data['name']} = ? AND ";
							$whereQueryParams[] = $data['value'];
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
						}
						
						break;
					
					default:
						$searchQuery .= "{$data['name']} LIKE ? AND ";
						$searchQueryParams[] = '%'.$data['value'].'%';
						break;
				}
				
			}
		}

		if(!empty($whereQuery) || !empty($searchQuery)) {

			$whereQuery = $whereQuery.$searchQuery = substr($searchQuery, 0, -4);

			$whereQueryParams = array_merge($whereQueryParams, $searchQueryParams);
		} else {
			throw ValidationException::withMessages(
				['fillLeastOneField' => 'En az bir alanı doldurmalısınız.']
			);
		}

		/*Order settings*/
	    $colIndex = $tblInfo['order'][0]['column'];
	    $colOrder = $tblInfo['columns'][$colIndex]['data'];
	    $order = $tblInfo['order'][0]['dir'];

		$dcDocumnets = DcDocuments::selectRaw($selectCol)
						->whereRaw($whereQuery, $whereQueryParams)
						->orderBy($colOrder, $order);

        
        $recordsTotal = DcDocuments::count();
	    $recordsFiltered = $dcDocumnets->count();

	    $data = $dcDocumnets->offset($tblInfo['start'])
					->limit($tblInfo['length'])
					->get();

	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $data,
	        'draw' => $tblInfo['draw']
	    ];
    }

	public function show(DcDocuments $dcDocuments)
	{
		return $dcDocuments;
	}
}
