<?php

namespace App\ModelsRepository;

trait Repository
{
    protected $lang;
    
    public function __construct(Array $id = [])
    {
        parent::__construct($id);
        $this->lang = \Illuminate\Support\Facades\App::getLocale();
    }

    public function scopeDataList($query, Array $info)
    {
        /*Select field Query*/
        $selectCol = array_diff(
            $info['selectCol'], 
            $info['addLangFields']
        );
        $select = 't0.'.implode(', t0.', $selectCol);

        /*Like Query*/
    	$search = "'%{$info['search']}%'";
    	$like = implode(
    		" LIKE {$search} OR ", 
    		$info['searchCol']
    	);
    	$like .= " LIKE {$search}";
    	
        /*From Query*/
    	$from = "{$info['table']} as t0";

        if(count($info['addLangFields']) > 0){

            /*Select Language field Query*/
            $select .= ', t1.';
            $select .= implode(', t1.', $info['addLangFields']);
            
            /*Language Inner Joın Query*/
            $langTbl = "{$info['table']}_lang";

            $join = [
                "{$langTbl} as t1", 
                "t0.{$info['fieldIDName']}", '=', 
                "t1.{$info['table']}_id"
            ];

            /*Language Choice*/
            $whereRaw = [
                "t1.{$info['fieldDependsOnLang']} = :lang", 
                ['lang' => $this->lang]
            ];
        }
        
        /* Custom join and select */
        if(isset($info['choiceJoin']) && isset($info['join']) && isset($info['selectJoin'])){
            $choiceJoin = $info['choiceJoin'];

            $query->$choiceJoin(...$info['join']);
            $select .= $info['selectJoin'];
        }
    
        $query->from($from)
        ->selectRaw($select);

        if(isset($join) && isset($whereRaw)){
            $query->join(...$join)
            ->whereRaw(...$whereRaw);
        }

        $query->orderBy($info['colOrder'], $info['order']);

        if($info['search']){
        	$query->whereRaw($like);
        }

        return $query;
    }

    public function scopeUpdateMany($query, Array $datas)
    {
        extract($datas);
        $childIDName = $childIDName ?? 'id';

        $childModels = collect([]);
        
        $destroyIDs = $this->$childName
                            ->pluck($childIDName)
                            ->toArray();

        foreach ($childDatas as $childData) {
            
            $childID = $childData[$childIDName] ?? -1;
            
            $index = array_search(
                $childID, 
                $destroyIDs
            );    
            
            if ($index !== false) {
                unset($destroyIDs[$index]);
                $childModels->push(
                    $this->$childName[$index]->fill($childData)
                );
            }else{
                $childModels->push(
                    (clone $childInstance)->fill($childData)
                );
            }
        }
        
        if(count($destroyIDs) > 0)
            $childInstance::destroy($destroyIDs);

        $bksl = $this->$childName()->saveMany($childModels);
        return $this->setRelations([$childName => $bksl]);
    }

    public function scopeUpdateBy($query, $model)
    {
        /* Günceleme süresini ve kişiyi ekleme başla */
        $user = auth()->user();
        $date = date("d-m-Y H:i:s");

        $model->updated_by = $user->id;
        $model->updated_by_name = "$date | $user->name ($user->email)";
        $model->save();
        /* Günceleme süresini ve kişiyi ekleme bitiş */
    }
}