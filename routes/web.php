<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\UnionsController;
use App\Http\Controllers\Admin\TeachersController;
use App\Http\Controllers\Admin\Search\SearchController;
use App\Http\Controllers\Admin\DocumentManagement\ListController;
use App\Http\Controllers\Admin\DocumentManagement\CommentController;
use App\Http\Controllers\Admin\LawsuitManagement\LawsuitsController;
use App\Http\Controllers\Admin\DocumentManagement\CategoryController;
use App\Http\Controllers\Admin\DocumentManagement\DocumentsController;
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
		->name('teachers.infos');

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
		->name('teachers.addExcelValidation');

		Route::post(
			'teachers/add-excel', 
			'addExcel'
		)
		->name('teachers.addExcel');
		
		Route::post(
			'teachers/store/excel', 
			'storeExcel'
		)
		->name('teachers.store.excel');
		
		Route::post(
			'teachers/store/images', 
			'storeImages'
		)
		->name('teachers.store.images');

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
        Route::get('/teacher-infos', 'showTeacherInfos')->name('teacherInfos');;
    });

Route::prefix('admin/old-regulation')
    ->middleware('auth')
    ->controller(OldSearchController::class)
    ->name('admin.old_regulation.')
    ->group(function () {
        Route::get('/expert-search', 'expertInfo')->name('search');;
        Route::post('/expert-search-list', 'getExpertInfos')->name('searchList');;
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
		->name('document.manualCreate');

        Route::post(
			'document/manual-store', 
			'manualStore'
		)
		->name('document.manualStore');

		Route::post(
			'document/udf-control', 
			'udfControl'
		)
		->name('document.udfControl');
        
        Route::get(
			'document/create', 
			'create'
		)
		->name('document.create');

        Route::post(
			'document/store', 
			'store'
		)
		->name('document.store');
        
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
        ->name('search.show');
    });

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
