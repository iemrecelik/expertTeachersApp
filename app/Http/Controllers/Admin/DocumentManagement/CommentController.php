<?php

namespace App\Http\Controllers\Admin\DocumentManagement;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin\DcComment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Responsable\isAjaxResponse;
use App\Http\Requests\Admin\DocumentManagement\StoreDcCommentRequest;
use App\Http\Requests\Admin\DocumentManagement\UpdateDcCommentRequest;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->toArray();

        $user = Auth::user();

        $datas = array_map(function($item) use ($user) {
            if($user->id === $item['id'] ) {
                $item['auth'] = true;
            }else {
                $item['auth'] = false;
            }
            return $item;
        }, $users);

        return view(
            'admin.document_mng.comment.index', 
            ['datas' => $datas]
        );
    }

    public function getDataList(Request $request)
	{
	    $tblInfo = $request->all();

        $selectUserId = $tblInfo['user_id'];

        $notSelectCol = [
            'user_name',
            'user_id',
            'dc_number',
            'dc_subject',
        ];

	    /*Array select and search columns*/
	    foreach ($tblInfo['columns'] as $column) {
	        
	        if (isset($column['data'])){
                if(!in_array($column['data'], $notSelectCol))
                    $selectCol[] = $column['data'];
            }

	        if($column['searchable'])
	            $searchCol[] = $column['data'];
	    }

	    /*Order settings*/
	    $colIndex = $tblInfo['order'][0]['column'];
	    $colOrder = $tblInfo['columns'][$colIndex]['data'];
	    $order = $tblInfo['order'][0]['dir'];


        /*join*/
        $join = [
            "users as t1", 
            "t0.user_id", '=', 
            "t1.id"
        ];

        $selectJoin = ", t1.name as user_name, t1.id as user_id";

	    $dataList = DcComment::dataList([
	        'table' => 'dc_comment',
	        'fieldIDName' => 'id',
	        'addLangFields' => [],
            'choiceJoin' => 'leftJoin',
            'join' => $join,
            'selectJoin' => $selectJoin,
	        'selectCol' => $selectCol,
	        'searchCol' => $searchCol,
	        'colOrder' => $colOrder,
	        'order' => $order,
	        'search' => $tblInfo['search']['value'],
	    ]);

        $dataList->leftJoin('dc_documents as t2', 't2.id', '=', 't0.dc_id');
        $dataList->selectRaw('t2.dc_number, t2.dc_subject, t2.id as dc_id');

        $dataList->leftJoin('dc_files as t3', 't3.dc_file_owner_id', '=', 't2.id');
		$dataList->selectRaw('t3.dc_file_path');

        if($selectUserId > 0) {
            $dataList->where('t0.user_id', $selectUserId);
        }

	    $recordsTotal = DcComment::count();
	    $recordsFiltered = $dataList->count();
        
	    $data = $dataList->offset($tblInfo['start'])
	    ->limit($tblInfo['length'])
	    ->get();

        /* Editing only own objects */
        $userId = $request->user()->id;

        $datas = array_map(function($item) use ($userId) {
            if($item['user_id'] === $userId) {
                return $item;
            }else {
                $item['id'] = null;
                return $item;
            }
        }, $data->toArray());
        
	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $datas,
	        'draw' => $tblInfo['draw']
	    ];
	}

    public function getComments(Request $request)
    {
        $params = $request->all();

        $dcComments = DcComment::where([
            ['dc_id', $params['dc_id']],
        ])
        ->with('user')
        ->get()
        ->toArray();

        if(!empty($dcComments)) {
            $userId = $request->user()->id;

            $dcComments = array_map(function($dcComment) use ($userId) {
                if($userId == $dcComment['user_id']) {
                    $dcComment['user'] = [
                        'name' => $dcComment['user']['name'],
                        'auth' => true
                    ];
                }else {
                    $dcComment['user'] = [
                        'name' => $dcComment['user']['name'],
                        'auth' => false
                    ];
                }

                return $dcComment;
            }, $dcComments);
        }

        return $dcComments;
    }

    public function addComment(Request $request)
    {
        /* 
            1- Bütün formlarda validation kontrolleri yapılacak
            +++ 2- Dökümanların ekleri gösterilecek ve resim , pdf dosyaları tarayıcı gösterilecek
            +++ 3- udf dosyası indirilebilir olacak 
            +++ 4- ana evrağın yanında ilgi evrakları da gözükecek
            +++ 5- Listeleri sadece yükleyen kişi silebilir.
            +++ 6- Tablolarda arama çubuğunu kaldır.
            +++ 7- evrak aramada ilgi evrağı göster deyince ana evrak ve ilgiler gözükecek
            +++ 8- listeye eklenen ilgi evrak aramada gösterilmiyor.
            9- ralative olan yerler relative olarak değiştirilecek.
            +++ 10- manual evrak ekleme yapılacak.
            +++ 11- admin panelde yükleme logosu değişecek.
            +++ 12- aranan kelime dosyada seçili olacak.
            13- ana evrağı gösterirken  konsol da path_file hatası veriyor.
            +++ 14- evrak eklerken liste ve not ekleme eklenecek.
            +++ 15- tablolarda sayfa sayıları kontrol edilecek
            +++ 16- Hem manual hemde otomotik yükleme birleştirilecek
            +++ 17- Aynı isimde veri eklenmeyecek
            18- Dudu PANK dosyasını yüklerken içindekileri düzgün çekmiyor.
            19- Evraklarda Yetkilendirme olacak
            +++ 20- Hangi kullanıcı tarafından ekleneceği
            +++ 21- login yaptıktan sonra evrak arama sayfasına girsin
            +++ 22. dış kurum evrakların içerikleri veri tbanınan kaydedilesin. Ve arama yapılabilsin.
            23. udf dosyalarını düzgün çekmiyor. Gönderene ilgi yazıyı da ekliyor.
            24. öğretmenler listesi ve crud işlemleri eklenecek
            25. teachers tablosundaki phone alanı mobile olarak değiştir
        */

        $request->validate(
            [
                'dc_com_text' => 'required|string'
            ],
            [
                'dc_com_text.required' => 'Not alanını boş bırakamazsınzı'
            ],
        );

        $params = $request->all();

        $dcComment = DcComment::where([
            ['dc_id', $params['dc_id']],
            ['user_id', $request->user()->id]
        ])->first();

        if($dcComment) {

            $dcComment->dc_com_text = $params['dc_com_text'];
            $dcComment->save();

        }else {
            $dcComment = DcComment::create([
                'dc_com_text'   => $params['dc_com_text'],
                'dc_id'         => $params['dc_id'],
                'user_id'       => $request->user()->id,
            ]);
        }

        return ['succeed' => __('messages.add_success')];
    }

    public function deleteComment(Request $request)
    {
        $params = $request->all();

        $res = DcComment::where([
            ['dc_id', $params['dc_id']],
            ['user_id', $request->user()->id]
        ])->delete();

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\StoreDcCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDcCommentRequest $request)
    {
        $params = $request->all();

        $params['user_id'] = $request->user()->id;

        DcComment::create($params);
    
        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\DcComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(DcComment $comment)
    {
        return new isAjaxResponse($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateDcCommentRequest  $request
     * @param  \App\Models\Admin\DcComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDcCommentRequest $request, DcComment $comment)
    {
        $params = $request->all();

        $comment->fill($params)->save();
    
        return [
            'updatedItem' => $comment,
            'succeed' => __('messages.edit_success')
        ];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DcComment $comment)
    {
        $res = $comment->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
