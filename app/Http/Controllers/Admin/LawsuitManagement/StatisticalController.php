<?php

namespace App\Http\Controllers\Admin\LawsuitManagement;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatisticalController extends Controller
{
    public function index()
    {
        /* Sendika toplam dava sayısı */
        $unsSum = DB::table('lawsuits as t0')
            ->where('t0.uns_id', '!=', null)
            ->count();
            
        /* Öğretmenlerin toplam dava sayısı */
        $thrSum = DB::table('lawsuits as t0')
            ->where('t0.thr_id', '!=', null)
            ->count();
        
        /* Sendikalar konularına göre sayılar */
        $sumBriefCount = DB::table('lawsuits as t0')
            ->selectRaw('t0.law_brief, count(*) as count')
            ->groupBy('t0.law_brief')
            ->get();

        /* Sendikalar konularına göre sayılar */
        $unsBriefCount = DB::table('lawsuits as t0')
            ->selectRaw('t0.law_brief, count(*) as count')
            ->where('t0.uns_id', '!=', null)
            ->groupBy('t0.law_brief')
            ->get();


        /* Öğretmenler konularına göre sayılar */
        $thrBriefCount = DB::table('lawsuits as t0')
            ->selectRaw('t0.law_brief, count(*) as count')
            ->where('t0.thr_id', '!=', null)
            ->groupBy('t0.law_brief')
            ->get();

        /* Sendikalar göre davaların sayıları */
        $unsCount = DB::table('lawsuits as t0')
            ->selectRaw('t1.uns_name, count(*) as count')
            ->join('unions as t1', 't0.uns_id', '=', 't1.id')
            ->where('t0.uns_id', '!=', null)
            ->groupBy('t1.uns_name')
            ->get();

        // dd($sumBriefCount->toArray());

        $unsNames = [];

        foreach ($sumBriefCount->toArray() as $key => $item) {
            $tableStats[$key]['law_brief'] = $item->law_brief;

            foreach ($thrBriefCount as $kthr => $vthr) {
                if($item->law_brief == $vthr->law_brief) {
                    $tableStats[$key]['thrCount'] = $vthr->count;
                    break; 
                }
            }

            $tableStats[$key]['unsCount'] = DB::table('lawsuits as t0')
                ->selectRaw('t1.uns_name, count(*) as count')
                ->join('unions as t1', 't0.uns_id', '=', 't1.id')
                ->where('t0.uns_id', '!=', null)
                ->where('t0.law_brief', '=', $item->law_brief)
                ->groupBy('t1.uns_name')
                ->orderBy('t1.uns_name', 'asc')
                ->get();

            $unsNameItem = array_map(function($item) {
                return $item->uns_name;
            }, $tableStats[$key]['unsCount']->toArray());

            /* $tableStats[$key]['unsNames'] = $tableStats[$key]['unsNames'] ?? [];
            $tableStats[$key]['unsNames'] = array_merge($tableStats[$key]['unsNames'], $unsNameItem); */

            $unsNames = array_merge($unsNames, $unsNameItem);
        }

        // dd($tableStats);

        $unsNames = array_unique($unsNames);
        sort($unsNames);

        // dd($unsNames);

        $stats = [
            'unsSum' => $unsSum,
            'thrSum' => $thrSum,
            'unsBriefCount' => $unsBriefCount,
            'thrBriefCount' => $thrBriefCount,
            'sumBriefCount' => $sumBriefCount,
            'unsCount' => $unsCount,
            'tableStats' => $tableStats,
            'unsNames' => $unsNames,
        ];

        // dd($stats);

        return view(
            'admin.lawsuits_mng.statistical.index', 
            [ 'stats' => $stats]
        );
    }
}