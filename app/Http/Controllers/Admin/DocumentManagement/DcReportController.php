<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Admin\DocumentManagement\StoreDocumentRecordCountRequest;

class DcReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view(
            'admin.document_mng.report.index',
            [
                'datas' => [
                    'users' => $users
                ]
            ]
        );
    }

    public function saveDocumentRecordCount(StoreDocumentRecordCountRequest $request)
    {
        dd($request->all());
    }
}
