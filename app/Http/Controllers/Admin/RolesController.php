<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin\Roles;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Responsable\isAjaxResponse;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Admin\StoreRolesRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\UpdateRolesRequest;

class RolesController extends Controller
{
    public function __construct() {
        $this->middleware(['role:super_admin']);
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
        return view('admin.roles.index');
    }

    public function rolesHasPermissions(Role $role)
    {
        return $role->permissions;
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

    public function updatePermissions(Request $request, Role $role)
    {
        $params = $request->all();

        $role->syncPermissions($params['perm_id']);
    
        return [
            'updatedItem' => $role->permissions,
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
        

	    $dataList = Roles::dataList([
	        'table' => 'roles',
	        'fieldIDName' => 'id',
	        'addLangFields' => [],
	        'selectCol' => $selectCol,
	        'searchCol' => $searchCol,
	        'colOrder' => $colOrder,
	        'order' => $order,
	        'search' => $tblInfo['search']['value'],
	    ]);

	    $recordsTotal = Roles::count();
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
     * @param  \App\Http\Requests\Admin\DocumentManagement\StoreRolesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRolesRequest $request)
    {
        $name = $request->input('name');

        Role::create(['name' => $name]);

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
     * @param  \App\Models\Admin\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function edit(Roles $role)
    {
        return new isAjaxResponse($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\DocumentManagement\UpdateRolesRequest  $request
     * @param  \App\Models\Admin\Roles $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRolesRequest $request, Roles $role)
    {
        $params = $request->all();

        $existRole = Roles::where('name', $params['name'])->count();

        if($existRole > 0) {
            throw ValidationException::withMessages(
                ['name' => 'Bu role ismi kayıtlı başka bir tane giriniz.']
            );
        }

        $role->fill($params)->save();
    
        return [
            'updatedItem' => $role,
            'succeed' => __('messages.edit_success')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Roles  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Roles $role)
    {
        $res = $role->delete();
        $msg = [];

        if ($res)
            $msg['succeed'] = __('delete_success');
        else
            $msg['error'] = __('delete_error');

        return $msg;
    }
}
