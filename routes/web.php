<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\DocumentManagement\CategoryController;
use App\Http\Controllers\Admin\DocumentManagement\DocumentsController;
use App\Http\Controllers\Admin\DocumentManagement\SearchController as DcSearchController;
use App\Http\Controllers\Admin\Search\SearchController;
use App\Http\Controllers\Admin\OldRegulation\SearchController as OldSearchController;

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

Route::get('/', function () {
    return view('welcome');
});

/* Route::get('/admin/document-management/category/get-category', function () {
    return view('welcome');
}); */


Route::resource('main', MainController::class);

// Route::get('search', [SearchController::class, 'expertInfo']);

Route::prefix('admin/search')
    ->controller(SearchController::class)
    ->name('admin.search.')
    ->group(function () {
        Route::get('/teacher-infos', 'showTeacherInfos')->name('teacherInfos');;
    });

Route::prefix('admin/old-regulation')
    ->controller(OldSearchController::class)
    ->name('admin.old_regulation.')
    ->group(function () {
        Route::get('/expert-search', 'expertInfo')->name('search');;
        Route::post('/expert-search-list', 'getExpertInfos')->name('searchList');;
    });

Route::prefix('admin/document-management')
    ->controller(DocumentsController::class)
    ->name('admin.document_mng.')
    ->group(function () {
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
    });
    
Route::prefix('admin/document-management')
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
    ->controller(DcSearchController::class)
    ->name('admin.document_mng.')
    ->group(function () {

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


