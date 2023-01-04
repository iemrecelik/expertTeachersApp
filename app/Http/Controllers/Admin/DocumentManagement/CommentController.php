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
            23- udf dosyalarını düzgün çekmiyor. Gönderene ilgi yazıyı da ekliyor.
            +++ 25. udf dosyaında göndereni ilgi olarak çekiyor.
            +++ 26. öğretmenler listesi ve crud işlemleri eklenecek
            +++ 27. teachers tablosundaki phone alanı mobile olarak değiştir
            +++ 28. treelselect dili türkçe ye çevirilecek
            +++ 29- Ortak liste yapılacak
            +++ 30- döküman kaydedersken öğretmen eklemek zorunlu olmasın
            +++ 31- Uzman öğretmen yazışmalarda evrak göster denilince kimin eklediğini göstermiyor.
            +++ 32- Davalar kısa konu bilgisi ayrı modül yapılacak
            +++ 33- Öğretmen eklerken uzman veya baş öğretmen bilgisi select box olacak
            +++ 34- Dava ekledikten sonra aynı popup da eklemeye kalktığımız undefined subOrders hatası veriyor.
            +++ 35- dava konuları editör olacak
            +++ 36- dava listesinde önlizleme butonu eklenecek
            37- pdf dosyalarını html kodlarına çevirilecek
            +++ 38- pdf çıktısını yatay sayfa olarak çıkartma
            +++ 39- excel listesini yüklemeden önce ön izleme olsun.
            +++ 40- excel listesini yüklerken sütunlardan en az bir tanesi seçili olması lazım.
            +++ 41- tc kimlik kontrolü yapılacak.
            41- tc kimlik kontrolü excel yüklemelerinde de yapılacak.
            +++ 42- excel olarak yüklerken select box la girilecek verilerin parantez ile bilgilendirme.
            43- excel ile yüklerken aynı verileri güncelle veya olmayanları ekle seçeneği kontrol edilecek.
            +++ 44- dava kaydettikten sonra hala bazı formlar temizlenmiyor.
            +++ 45- tarihler türkiye saatine ayarlanacak
            +++ 46- dava eklerken farklı yazılar eklendiğinde Duplicate keys hatası veriyor.
            +++ 47- dava modülünde eklemedeki kısa dava konusundaki treeselect düzenleme formunda da yapılacak.
            +++ 48- öğretmen detay görüntülemede davalar sekmesi olacak burada yazısmalara ait ekler dosya ekleri olarak eklenecek
            +++ 49- öğretmenler listesinde öğretmen detayına git kutucuğu olacak
            +++ 50- listelere filtreler eklenecek
            +++ 51- dava eklerken dava konularını eklemiyor. Daha çok evrak ekleme yaptığımzda yapıyor.
            +++ 52- öğretmen detayında resim yoksa varsayılan resim olacak.
            +++ 53- dava listesinde bireysel davalarda isim soyisim ve tc olacak şekilde gözüksün
            +++ 54- dava modülüne eklenen yazıların ekleri dava dosyası olarak kaydedilecek
            +++ 55- excel dosyası yüklerken bazı alanların excel dosyasında nasıl kaydedildikleri belirtilmesi lazım.
            56- ilişkili tablolarda silinen veri diğer tabloda silinmesi gerekiyor.
            +++ 57- dava dosyasını sildiğimiz zaman ekranı yenilemeden değişikleri göstersin.
            +++ 58. bilgi notuna bireysel dava eklenemesin
            59- evrak eklerken hangi sendikayı ilgilendiriyorsa oda eklensin
            +++ 60- Excel veri yüklerken var olan veriler silinmesin sadece güncellensin
            +++ 61- öğretmenleri sildiğimiz resimleri de silinsin
            +++ 62- dava ekledikten sonra bilgi notu eklenmiyor
            +++ 63- Filtre de iller veritabanına eklenecek
            64- listeler excel olarak çıkartılabilicek
            +++ 65- öğretmen ekleme formundakiler düzenleme formuna da eklenecek
            +++ 66- Öğretmen eklerken hem ekleme hem de düzenleme formuna il ve ilçe ekleme eklenecek
            67- İller ve ilçeler uygululama yüklerken sabit değer olarak yüklensin provider, service, middleware vb. laravelin araçlarına bak.
            +++ 68- Öğretmen Güncelleme de sorunlar düzeltilecek.
            +++ 69- Öğretmene sonradan yazı atama olacak
            70- Evrak ekleme yaparken manuel dediğimizde textbox larda filtreleme yapılsın.
            +++ 71- öğretmen eklerken consolda javascript hataları çıkıyor bunlara bakılacak
            +++ 72- evrak sayısı her yıl sıfırlanıyor. Bu yüzden tarih ve evrak sayısına göre kontrol yapılacak.
            +++ 73- Öğretmen eklerken zorun olanlar * olacak.
            +++ 74- excel veri eklerken aynı tc numarası varsa kontrol etsin
            +++ 75- excel formundaki zorunlu alanlar * ile belirtilsin.
            +++ 76- Öğretmen Güncelleme de sorunlar düzeltilecek.
            +++ 77- excel ile yükleme yaparken tc kontrolü olacak
            +++ 78- eklenmeyen resimlerin tc numaraları alt satıra geçecek
            +++ 79- aynı evrağa sonradan başka öğretmende eklenebilsin.
            +++ 80- evrak silme ve düzenleme olsun.
            +++ 81- kullanıcı yönetim paneli yapılacak
            +++ 82- evrak kayıt raporu eklenecek
            83- excel veya toplu ekleme ve düzenlemelerde timestamp yüklenecek
            +++ 84- yüklenen resimler zaten varsa öncekiler silinsin
            +++ 85- dava istatistikleri tarihe göre yapılsın.
            +++ 86- dava modülünde aynı evrak başka öğretmenlerede atanabilsin
            +++ 87- Evraklardan main status durumu kaldırılacak. Bütün evrak lar ana evrak olacak
            +++ 88- Evrak eklemedeki veri tabanındaki evrağı ilgi gösterme güncelleme formuna da eklenecek
            89- bilgisayara kodlar yüklendikten sonra kodları ilgilendiren dosya yı girmek kopyalama gibi işlemler için şifre istesin
            +++ 90- formlarda treeselect de evrak ekleme yaparken id numarasına göre kontrol etsin
            +++ 91- evrak eklerken genel müdür ve vekillerin kontrolü yapılacak ve de tarih kontrolü yapılacak
            +++ 92- güncelle yaparken güncelle butonu pasif çıkıyor
            +++ 93- kategori isimleri listelerde gösterilsin
            94- Tablodaki listelere sıra numarası verilecek
            +++ 95- kategori düznlerken üst kadegori yoktur seçeneği gelsin
            96- Dava kısa açıklamasına bütün kısa açıklamaları gelsin.
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
