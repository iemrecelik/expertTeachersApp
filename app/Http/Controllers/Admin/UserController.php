<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responsable\isAjaxResponse;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct() {
        /* $this->middleware(['permission:create categories'])->only('store');
        $this->middleware(['permission:edit categories'])->only('edit');
        $this->middleware(['permission:edit categories'])->only('update');
        $this->middleware(['permission:delete categories'])->only('destroy'); */
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.user.index');
    }

    public function userHasPermissions(User $user)
    {
        return $user->getAllPermissions();
    }

    public function getPermission(Request $request)
	{
        $permissions = Permission::all();

        $datas = [];

        foreach ($permissions as $key => $val) {
            $perm = explode(" ", $val['name']);
            $permName = $this->getTransPermName(end($perm));

            $datas[$permName][] = [
                'label' => $this->getTransPermName($val['name']),
                'id' => $val['name']
            ];
        }

        return $datas;
    }

    private function getTransPermName($name)
    {
        $permissions = [
            'documents' => 'documents_module',
            'show module documents' => 'Evrak Modülü Yetkisi',
            'show documents' => 'Evrak Göster',
            'create documents' => 'Evrak Ekleme',
            'edit documents' => 'Evrak Düzenleme',
            'delete documents' => 'Evrak Silme',
            
            'categories' => 'categories_module',
            'show module categories' => 'Kategori Modülü Yetkisi',
            'show categories' => 'Kategori Göster',
            'create categories' => 'Kategori Ekleme',
            'edit categories' => 'Kategori Düzenleme',
            'delete categories' => 'Kategori Silme',
            
            'lawsuits' => 'lawsuits_module',
            'show module lawsuits' => 'Dava Modülü Yetkisi',
            'show lawsuits' => 'Dava Gösterme',
            'create lawsuits' => 'Dava Ekleme',
            'edit lawsuits' => 'Dava Düzenleme',
            'delete lawsuits' => 'Dava Silme',
            
            'teachers' => 'teachers_manage_module',
            'show module teachers' => 'Öğretmen Yönetimi Yetkisi',
            'show teachers' => 'Öğretmen Gösterme',
            'create teachers' => 'Öğretmen Ekleme',
            'create excel teachers' => 'Excel ile Öğretmenleri Ekleme',
            'create images teachers' => 'Resim Dosyalarını Yükleme',
            'edit teachers' => 'Öğretmen Düzenleme',
            'delete teachers' => 'Öğretmen Silme',

            'document_record_reports' => "document_record_reports",
            'processes document_record_reports' => "Evrak Kayıt Raporu İşlemleri"
        ];

        return $permissions[$name];
    }

    public function updatePermissions(Request $request, User $user)
    {
        $params = $request->all();

        $user->syncPermissions($params);
    
        return [
            'updatedItem' => $user->getAllPermissions(),
            'succeed' => __('messages.edit_success')
        ];
    }
        
    public function getDataList(Request $request)
	{
	    $tblInfo = $request->all();

	    /*Array select and search columns*/
	    foreach ($tblInfo['columns'] as $column) {
	        
	        if (isset($column['data']))
	            $selectCol[] = $column['data'];

	        if($column['searchable'])
	            $searchCol[] = $column['data'];
	    }

	    /*Order settings*/
	    $colIndex = $tblInfo['order'][0]['column'];
	    $colOrder = $tblInfo['columns'][$colIndex]['data'];
	    $order = $tblInfo['order'][0]['dir'];
        

	    $dataList = User::dataList([
	        'table' => 'users',
	        'fieldIDName' => 'id',
	        'addLangFields' => [],
	        'selectCol' => $selectCol,
	        'searchCol' => $searchCol,
	        'colOrder' => $colOrder,
	        'order' => $order,
	        'search' => $tblInfo['search']['value'],
	    ]);

	    $recordsTotal = User::count();
	    $recordsFiltered = $dataList->count();
        
	    $data = $dataList->offset($tblInfo['start'])
	    ->limit($tblInfo['length'])
	    ->get();
        
	    return [
	        'recordsTotal' => $recordsTotal, 
	        'recordsFiltered' => $recordsFiltered, 
	        'data' => $data,
	        'draw' => $tblInfo['draw']
	    ];
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $params = $request->all();

        $dcCat = DcCategory::where('dc_cat_name', $params['dc_cat_name']);

        if(!empty($dcCat->first())) {
            throw ValidationException::withMessages(
                ['dc_cat_name' => 'Aynı isimde konu ekleyemezsiniz.']
            );
        }

        $childCategory = DcCategory::create($params);

        if($params['dc_cat_id'] > 0) {

            $dcCategory = DcCategory::find($params['dc_cat_id']);
            
            $dcCategory->childCategory()->save($childCategory);
        }
    
        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(DcCategory $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user->roles;
        return new isAjaxResponse($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateUserRequest  $request
     * @param  \App\Models\Admin\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $params = $request->all();

        $existUser = User::where('email', $params['email'])
                        ->with('roles')
                        ->first();

        if($existUser->id !== $user->id) {
            throw ValidationException::withMessages(
                ['email' => 'Bu email adresi kayıtlı başka bir tane giriniz.']
            );
        }

        $user->fill($params)->save();

        foreach ($user->roles as $roleKey => $rolVal) {
            if($rolVal['name'] == 'super_admin') {
                throw ValidationException::withMessages(
                    ['role' => 'Bu kullanıcıda değişiklik yapamazsınız.']
                );  
            }
        }

        if(!empty($params['role_name'])) {
            $user->assignRole($params['role_name']);
        }else {
            $user->syncRoles([]);
        }
    
        return [
            'updatedItem' => $user,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\DcCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DcCategory $category)
    {
        $res = $category->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
