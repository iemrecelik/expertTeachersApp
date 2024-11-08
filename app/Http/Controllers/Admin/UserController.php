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
        $roles = \App\Models\Admin\Roles::all();

        return view('admin.user.index', [
            'datas' => [
                'roles' => $roles
            ]
        ]);
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
            'documents'             => 'documents_module',
            'show module documents' => 'Evrak Modülü Yetkisi',
            'show documents'        => 'Evrak Göster',
            'create documents'      => 'Evrak Ekleme',
            'edit documents'        => 'Evrak Düzenleme',
            'delete documents'      => 'Evrak Silme',
            
            'categories'             => 'categories_module',
            'show module categories' => 'Kategori Modülü Yetkisi',
            'show categories'        => 'Kategori Göster',
            'create categories'      => 'Kategori Ekleme',
            'edit categories'        => 'Kategori Düzenleme',
            'delete categories'      => 'Kategori Silme',

            'unions'             => 'unions_module',
            'show module unions' => 'Sendika Modülü Yetkisi',
            'show unions'        => 'Sendika Göster',
            'create unions'      => 'Sendika Ekleme',
            'edit unions'        => 'Sendika Düzenleme',
            'delete unions'      => 'Sendika Silme',
            
            'institutions'              => 'institutions_module',
            'show module institutions'  => 'Kurum Modülü Yetkisi',
            'show institutions'         => 'Kurum Göster',
            'create institutions'       => 'Kurum Ekleme',
            'edit institutions'         => 'Kurum Düzenleme',
            'delete institutions'       => 'Kurum Silme',
            
            'lawsuits'                  => 'lawsuits_module',
            'show module lawsuits'      => 'Dava Modülü Yetkisi',
            'show lawsuits'             => 'Dava Gösterme',
            'create lawsuits'           => 'Dava Ekleme',
            'edit lawsuits'             => 'Dava Düzenleme',
            'delete lawsuits'           => 'Dava Silme',
            'show statistical lawsuits' => 'Dava İstatistiklerini Göster',

            'list'              => 'list_module',
            'show module list'  => 'liste Modülü Yetkisi',
            'show list'         => 'Liste Gösterme',
            'create list'       => 'Liste Ekleme',
            'edit list'         => 'Liste Düzenleme',
            'delete list'       => 'Liste Silme',

            'comment'                       => 'comment_module',
            'show module document comment'  => 'Not Modülü Yetkisi',
            'show document comment'         => 'Not Gösterme',
            'create document comment'       => 'Not Ekleme',
            'edit document comment'         => 'Not Düzenleme',
            'delete document comment'       => 'Not Silme',
            
            'teachers'                  => 'teachers_manage_module',
            'show module teachers'      => 'Öğretmen Yönetimi Yetkisi',
            'show teachers'             => 'Öğretmen Gösterme',
            'create teachers'           => 'Öğretmen Ekleme',
            'create excel teachers'     => 'Excel ile Öğretmenleri Ekleme',
            'create images teachers'    => 'Resim Dosyalarını Yükleme',
            'edit teachers'             => 'Öğretmen Düzenleme',
            'delete teachers'           => 'Öğretmen Silme',
            'create law to teachers'    => 'Öğretmene Dava Ekleme',
            'delete law to teachers'    => 'Öğretmenden Dava Silme',
            'add document teachers'     => 'Öğretmene Evrak Ekleme',
            'delete document teachers'  => 'Öğretmenden Evrak Silme',

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
        $params['password'] = \Illuminate\Support\Facades\Hash::make($params['password']);

        User::create($params);
    
        return ['succeed' => __('messages.add_success')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
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
            $user->syncRoles($params['role_name']);
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $res = $user->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
