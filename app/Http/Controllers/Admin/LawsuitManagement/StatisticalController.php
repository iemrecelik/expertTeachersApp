<?php

namespace App\Http\Controllers\Admin\LawsuitManagement;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatisticalController extends Controller
{
    public function index(Request $request)
    {
        $years = $request->input('years');
// dd($years);
        if($years) {
            $firstYear = min($years);
            $lastYear = max($years);
        }else {
            $firstYear = date('Y');
            $lastYear = date('Y');
        }

        /* Sendika toplam dava sayısı */
        $unsSum = DB::table('lawsuits as t0')
            ->join('dc_documents as t1', 't1.id', '=', 't0.dc_id')
            ->where('t0.uns_id', '!=', null)
            ->whereBetween('t1.dc_date', [strtotime('01.01.'.$firstYear), strtotime('31.12.'.$lastYear)])
            ->count();
            
        /* Öğretmenlerin toplam dava sayısı */
        $thrSum = DB::table('lawsuits as t0')
            ->join('dc_documents as t1', 't1.id', '=', 't0.dc_id')
            ->where('t0.thr_id', '!=', null)
            ->whereBetween('t1.dc_date', [strtotime('01.01.'.$firstYear), strtotime('31.12.'.$lastYear)])
            ->count();
        
        /* Sendikalar konularına göre sayılar */
        $sumBriefCount = DB::table('lawsuits as t0')
            ->selectRaw('t0.law_brief, count(*) as count')
            ->join('dc_documents as t1', 't1.id', '=', 't0.dc_id')
            ->whereBetween('t1.dc_date', [strtotime('01.01.'.$firstYear), strtotime('31.12.'.$lastYear)])
            ->groupBy('t0.law_brief')
            ->get();

        /* Sendikalar konularına göre sayılar */
        $unsBriefCount = DB::table('lawsuits as t0')
            ->selectRaw('t0.law_brief, count(*) as count')
            ->join('dc_documents as t1', 't1.id', '=', 't0.dc_id')
            ->where('t0.uns_id', '!=', null)
            ->whereBetween('t1.dc_date', [strtotime('01.01.'.$firstYear), strtotime('31.12.'.$lastYear)])
            ->groupBy('t0.law_brief')
            ->get();

        /* Öğretmenler konularına göre sayılar */
        $thrBriefCount = DB::table('lawsuits as t0')
            ->join('dc_documents as t1', 't1.id', '=', 't0.dc_id')
            ->selectRaw('t0.law_brief, count(*) as count')
            ->where('t0.thr_id', '!=', null)
            ->whereBetween('t1.dc_date', [strtotime('01.01.'.$firstYear), strtotime('31.12.'.$lastYear)])
            ->groupBy('t0.law_brief')
            ->get();

        /* Sendikalar göre davaların sayıları */
        $unsCount = DB::table('lawsuits as t0')
            ->selectRaw('t1.uns_name, count(*) as count')
            ->join('unions as t1', 't0.uns_id', '=', 't1.id')
            ->join('dc_documents as t2', 't2.id', '=', 't0.dc_id')
            ->where('t0.uns_id', '!=', null)
            ->whereBetween('t2.dc_date', [strtotime('01.01.'.$firstYear), strtotime('31.12.'.$lastYear)])
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
            'tableStats' => $tableStats ?? [],
            'unsNames' => $unsNames,
        ];

        // dd($stats);

        $years = array_map(function($item) {
            return [
                'id' => $item,
                'label' => $item
            ];
        }, $years);

        return view(
            'admin.lawsuits_mng.statistical.index', 
            [
                'stats' => $stats,
                'years' => $years,
            ]
        );
    }

    public function writeStatstoPDF(Request $request)
    {
        // return view('admin.lawsuits_mng.statistical.deneme');

        $request->validate(
            [
                'statsHtml' => 'required|string'
            ],
            [
                'statsHtml.required' => 'İstatistik alanını boş bırakamazsınzı'
            ],
        );

        $statsHtml = $request->input('statsHtml');
        $statsCss = $request->input('statsCss');
        $statsLandscape = $request->input('statsLandscape');

        $mpdf = New \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir')]);
        if($statsLandscape) {
            $mpdf->AddPage('L');
        }
        $mpdf->WriteHTML($statsCss, 1);
        $mpdf->WriteHTML($statsHtml, 2);

        $mpdf->Output();
    }
}