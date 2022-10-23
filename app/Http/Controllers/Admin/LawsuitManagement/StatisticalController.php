<?php

namespace App\Http\Controllers\Admin\LawsuitManagement;

use App\Http\Controllers\Controller;

class StatisticalController extends Controller
{
    public function index()
    {
        return view('admin.lawsuits_mng.statistical.index');
    }
}