<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UnionsController;
use App\Http\Controllers\Admin\TeachersController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MySettingsController;
use App\Http\Controllers\Admin\Search\SearchController;
use App\Http\Controllers\Admin\DocumentManagement\ListController;
use App\Http\Controllers\Admin\DocumentManagement\CommentController;
use App\Http\Controllers\Admin\LawsuitManagement\LawsuitsController;
use App\Http\Controllers\Admin\DocumentManagement\CategoryController;
use App\Http\Controllers\Admin\DocumentManagement\DocumentsController;
use App\Http\Controllers\Admin\DocumentManagement\DcReportController;
use App\Http\Controllers\Admin\DocumentManagement\DcWaitingController;
use App\Http\Controllers\Admin\LawsuitManagement\StatisticalController;
use App\Http\Controllers\Admin\TeachersListManagement\InstitutionsController;
use App\Http\Controllers\Admin\OldRegulation\SearchController as OldSearchController;
use App\Http\Controllers\Admin\DocumentManagement\SearchController as DcSearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

/* Route::get('/admin/document-management/category/get-category', function () {
    return view('welcome');
}); */


// Route::resource('main', MainController::class)->middleware('auth');

// Route::get('search', [SearchController::class, 'expertInfo']);
Route::prefix('admin')
    ->middleware('auth')
    ->controller(TeachersController::class)
    ->name('admin.')
    ->group(function () {
		Route::post(
			'teachers/infos', 
			'showTeacherInfos'
		)
		->middleware(['permission:show teachers'])
		->name('teachers.infos');
		
		Route::post(
			'teachers/infos/add-document-teacher', 
			'addDocumentToTeacher'
		)
		->middleware(['permission:add document teachers'])
		->name('teachers.infos.addDocumentToTeacher');

		Route::delete(
			'teachers/infos/del-document-teacher/{teacher}/{document}', 
			'delDocumentToTeacher'
		)
		->where([
			'teacher' => '[0-9]+',
			'document' => '[0-9]+'
		])
		->middleware(['permission:delete document teachers'])
		->name('teachers.infos.delDocumentToTeacher');

		Route::post(
			'teachers/data-list', 
			'getDataList'
		)
		->name('teachers.dataList');
		
		Route::get(
			'teachers/search-list', 
			'getSearchTeacherList'
		)
		->name('teachers.searchList');

		Route::post(
			'teachers/add-excel-validation', 
			'addExcelValidation'
		)
		->name('teachers.addExcelValidation')
		->middleware(['permission:create excel teachers']);

		Route::post(
			'teachers/add-excel', 
			'addExcel'
		)
		->name('teachers.addExcel')
		->middleware(['permission:create excel teachers']);
		
		Route::post(
			'teachers/store/excel', 
			'storeExcel'
		)
		->middleware(['permission:create excel teachers'])
		->name('teachers.store.excel');
		
		Route::post(
			'teachers/store/images', 
			'storeImages'
		)
		->name('teachers.store.images')
		->middleware(['permission:create images teachers']);

		Route::post(
			'teachers/export-excel-datas', 
			'exportExcelDatas'
		)
		->name('teachers.exportExcelDatas');

		Route::post(
			'teacherInfos/add-law-file-name', 
			'addLawFile'
		)
		->middleware(['permission:create law to teachers'])
		->name('teachers.teacherInfos.addLawFile');

		Route::get(
			'teachers/get-province-list', 
			'getProvincesList'
		)
		->name('teachers.getProvincesList');

		Route::get(
			'teachers/get-towns-list', 
			'getTownsList'
		)
		->name('teachers.getTownsList');

		Route::delete(
			'teacherInfos/add-law-file-name/{lawsuitFile}', 
			'deleteLawFile'
		)
		->middleware(['permission:delete law to teachers'])
		->where(['lawsuitFile' => '[0-9]+']);

		Route::put(
			'teachers/store/with-mebbis',
			'storeWithMebbis'
		)
		->middleware(['role:super_admin'])
		->name('teachers.store.withMebbis');

		Route::resource('teachers', TeachersController::class);
    });

Route::prefix('admin')
    ->middleware('auth')
    ->controller(InstitutionsController::class)
    ->name('admin.')
    ->group(function () {
		/* Institutions */
        Route::get(
			'institutions/get-institutions', 
			'getInstitutions'
		)
		->name('institutions.getInstitutions');

        Route::post(
			'institutions/data-list', 
			'getDataList'
		)
		->name('institutions.dataList');

		Route::resource('institutions', InstitutionsController::class);
    });

Route::prefix('admin')
    ->middleware('auth')
    ->controller(UnionsController::class)
    ->name('admin.')
    ->group(function () {
		/* Unions */
        Route::get(
			'unions/get-unions', 
			'getUnions'
		)
		->name('unions.getUnions');

        Route::post(
			'unions/data-list', 
			'getDataList'
		)
		->name('unions.dataList');

		Route::get(
			'unions/search-list', 
			'getSearchUnionList'
		)
		->name('unions.searchList');

		Route::resource('unions', UnionsController::class);
    });

Route::prefix('admin/search')
    ->middleware('auth')
    ->controller(SearchController::class)
    ->name('admin.search.')
    ->group(function () {
        Route::get('/teacher-infos', 'showTeacherInfos')->name('teacherInfos');
    });

Route::prefix('admin/old-regulation')
    ->middleware('auth')
    ->controller(OldSearchController::class)
    ->name('admin.old_regulation.')
    ->group(function () {
        Route::get('/expert-search', 'expertInfo')->name('search');
        Route::post('/expert-search-list', 'getExpertInfos')->name('searchList');
    });

Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(DcReportController::class)
    ->name('admin.document_mng.')
    ->group(function () {
        /* Report */
        Route::get(
			'report', 
			'index'
		)
		->middleware(['permission:processes document_record_reports'])
		->name('report.index');

        Route::post(
			'report/record-count', 
			'saveDocumentRecordCount'
		)
		->middleware(['permission:processes document_record_reports'])
		->name('report.saveDocumentRecordCount');

		Route::get(
			'report/document-on-date', 
			'getDocumentOnDate'
		)
		->name('report.getDocumentOnDate');

		Route::get(
			'report/report-count-on-date',
			'getReportCountOnDate'
		)
		->name('report.getReportCountOnDate');
		
		Route::get(
			'report/record-need-documents',
			'getRecordNeedDocuments'
		)
		->name('report.getRecordNeedDocuments');
    });

Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(DocumentsController::class)
    ->name('admin.document_mng.')
    ->group(function () {
        /* Document */
        Route::get(
			'document/manual-create', 
			'manualCreate'
		)
		->name('document.manualCreate')
		->middleware(['permission:create documents']);

        Route::post(
			'document/manual-store', 
			'manualStore'
		)
		->name('document.manualStore')
		->middleware(['permission:create documents']);

		/* Route::post(
			'document/validation-manual-store', 
			'validateManuelStore'
		)
		->name('document.validateManuelStore')
		->middleware(['permission:create documents']); */

		Route::post(
			'document/udf-control', 
			'udfControl'
		)
		->name('document.udfControl');
        
        Route::get(
			'document/create', 
			'create'
		)
		->name('document.create')
		->middleware(['permission:create documents']);

        Route::post(
			'document/store', 
			'store'
		)
		->name('document.store')
		->middleware(['permission:create documents']);
		
		Route::get(
			'document/edit/{document}', 
			'edit'
		)
		->where('document', '[0-9]+')
		->name('document.edit')
		->middleware(['permission:edit documents']);

        Route::post(
			'document/update', 
			'update'
		)
		->name('document.update');
        
        Route::post(
			'document/get-file-infos', 
			'getFileInfos'
		)
		->name('document.getFileInfos');

		Route::get(
			'document/search-list', 
			'getDocumentSearchList'
		)
		->name('document.searchList');

		Route::delete(
			'document/{document}', 
			'deleteDocument'
		)
		->where(['document' => '[0-9]+'])
		->middleware(['permission:delete documents']);
    });

Route::prefix('admin/document-management')
    ->middleware('auth', 'role:super_admin')
    ->controller(DcWaitingController::class)
    ->name('admin.document_mng.')
    ->group(function () {
		Route::get(
			'waiting/list', 
			'index'
		)
		->name('waiting.index');

		Route::get(
			'waiting/get-docs', 
			'getWaitingDocument'
		)
		->name('waiting.getWaitingDocument');

		Route::post(
			'waiting/save-bot-docs', 
			'saveBotDocument'
		)
		->name('waiting.saveBotDocument');
	});
    
Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(CategoryController::class)
    ->name('admin.document_mng.')
    ->group(function () {
        /* Category */
        Route::get(
			'category/get-category', 
			'getCategory'
		)
		->name('category.getCategory');

        Route::post(
			'category/data-list', 
			'getDataList'
		)
		->name('category.dataList');

        Route::resource('category', CategoryController::class);
    });

Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(ListController::class)
    ->name('admin.document_mng.')
    ->group(function () {
        /* List */
        Route::get(
			'list/get-list', 
			'getList'
		)
		->name('list.getList');
		
		Route::get(
			'list/get-req-list', 
			'getReqList'
		)
		->name('list.getReqList');

        Route::get(
			'list/get-list-and-selected', 
			'getListAndSelected'
		)
		->name('list.getListAndSelected');

        Route::post(
			'list/data-list', 
			'getDataList'
		)
		->name('list.dataList');

        Route::post(
			'list/add-list', 
			'addList'
		)
		->name('list.addList');
        
        Route::post(
			'list/delete-list', 
			'deleteList'
		)
		->name('list.deleteList');

        Route::resource('list', ListController::class);
    });

Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(CommentController::class)
    ->name('admin.document_mng.')
    ->group(function () {
        /* Comment */
        Route::post(
			'comment/add-comment', 
			'addComment'
		)
		->name('comment.addComment');
        
        Route::get(
			'comment/get-comments', 
			'getComments'
		)
		->name('comment.getComments');

        Route::post(
			'comment/delete-comment', 
			'deleteComment'
		)
		->name('comment.deleteComment');

        Route::post(
			'comment/data-list', 
			'getDataList'
		)
		->name('comment.dataList');

        Route::resource('comment', CommentController::class);
    });

Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(DcSearchController::class)
    ->name('admin.document_mng.')
    ->group(function () {

        Route::post(
			'search/get-category-and-list', 
			'getCategoryAndList'
		)
		->name('search.getCategoryAndList');

         Route::get(
			'search/search-form', 
			'searchForm'
		)
		->middleware(['permission:show module documents'])
		->name('search.searchForm');
        
        Route::post(
			'search/get-search-documents', 
			'getSearchDocuments'
		)
		->name('search.getSearchDocuments');

        Route::get(
            "search/{dcDocuments}",
            "show"
        )
        ->where('dcDocuments', '[0-9]+')
        ->name('search.show')
		->middleware(['permission:show documents']);
    });

/* Route::prefix('admin/document-management')
    ->middleware('auth')
    ->controller(DocumentsController::class)
    ->name('admin.document_mng.')
    ->group(function () {

        Route::get(
			'waiting/list', 
			'getWaitingDocument'
		)
		->name('waiting.getWaitingDocument');
    }); */

Route::prefix('admin/lawsuit-management')
    ->middleware('auth')
    ->controller(LawsuitsController::class)
    ->name('admin.lawsuit_mng.')
    ->group(function () {
        /* Lawsuits */
		Route::post(
			'lawsuits/data-list', 
			'getDataList'
		)
		->name('lawsuits.dataList');

		Route::get(
			'lawsuits/search-list', 
			'getLawBriefSearchList'
		)
		->name('lawsuits.searchList');
		
		Route::post(
			'lawsuits/law-infos', 
			'lawInfos'
		)
		->name('lawsuits.lawInfos');

		Route::resource('lawsuits', LawsuitsController::class);
    });

Route::prefix('admin/lawsuit-management')
    ->middleware('auth')
    ->controller(StatisticalController::class)
    ->name('admin.lawsuit_mng.')
    ->group(function () {
        /* Statistical */
		Route::post(
			'statistical/data-list', 
			'getDataList'
		)
		->name('statistical.dataList');

		Route::get(
			'statistical', 
			'index'
		)
		->name('statistical.index');

		Route::post(
			'statistical/stats-to-pdf', 
			'writeStatstoPDF'
		)
		->name('statistical.statsToPdf');

		/* Route::get(
			'statistical/stats-to-pdf2', 
			'writeStatstoPDF'
		)
		->name('statistical.statsToPdf2'); */
    });

Route::prefix('admin')
    ->middleware(['auth', 'role:admin|auth_admin|super_admin'])
    ->controller(UserController::class)
    ->name('admin.')
    ->group(function () {
        /* User */
		Route::get(
			'permission/get-permission', 
			'getPermission'
		)
		->name('permission.getPermission');
		
		Route::get(
			'user/{user}/edit-permissions',
			'userHasPermissions'
		)
		->name('permission.editPermissions')
		->where([
			'user' => '[0-9]+',
		]);

		Route::put(
			'user/{user}/update-permissions', 
			'updatePermissions'
		)
		->name('user.updatePermissions');

        Route::post(
			'user/data-list', 
			'getDataList'
		)
		->name('user.dataList');

        Route::resource('user', UserController::class);
    });

	Route::prefix('admin/logs')
    ->middleware(['auth', 'role:auth_admin|super_admin'])
    ->controller(LogsController::class)
    ->name('admin.logs.')
    ->group(function () {
        /* Logs */
		Route::get(
			'/', 
			'index'
		)
		->name('index');

		Route::post(
			'list', 
			'getLogsList'
		)
		->name('getLogsList');
    });

	Route::prefix('admin/settings')
    ->middleware(['auth', 'role:auth_admin|super_admin'])
    ->controller(SettingsController::class)
    ->name('admin.settings.')
    ->group(function () {
        /* Settings */
		Route::get(
			'/', 
			'index'
		)
		->name('index');

		Route::put(
			'update', 
			'update'
		)
		->name('update');
    });
	
Route::prefix('admin/my-settings')
    ->middleware(['auth'])
    ->controller(MySettingsController::class)
    ->name('admin.mySettings.')
    ->group(function () {
        /* Settings */
		Route::get(
			'/', 
			'index'
		)
		->name('index');

		Route::put(
			'update', 
			'update'
		)
		->name('update');
    });