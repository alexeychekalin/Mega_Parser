<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'User', 'prefix' => 'users', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\User\UserController::class, 'index']);
    Route::get('/', [App\Http\Controllers\User\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\User\UserController::class, 'update'])->name('user.update');
    Route::post('/delete', [App\Http\Controllers\User\UserController::class, 'delete'])->name('user.delete');
    Route::post('/change', [App\Http\Controllers\User\UserController::class, 'changePassword'])->name('user.change');
    Route::post('/set', [App\Http\Controllers\User\UserController::class, 'set'])->name('user.set');
});

Route::group(['namespace' => 'Log', 'prefix' => 'log', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\LogController::class, 'index']);
    Route::post('/get', [App\Http\Controllers\LogController::class, 'readLogs'])->name('log.get');
});

Route::group(['namespace' => 'Edits', 'prefix' => 'edits', 'middleware' => 'auth:sanctum'], function (){
    Route::get('/', [App\Http\Controllers\EditsController::class, 'index']);
    Route::post('/', [App\Http\Controllers\EditsController::class, 'insert']);
    Route::post('/delete', [App\Http\Controllers\EditsController::class, 'delete'])->name('edits.delete');
    Route::post('/update', [App\Http\Controllers\EditsController::class, 'update'])->name('edits.update');
});

Route::group(['namespace' => 'Trash', 'prefix' => 'trash', 'middleware' => 'auth:sanctum'], function (){
    Route::get('/', [App\Http\Controllers\TrashController::class, 'index']);
    Route::post('/', [App\Http\Controllers\TrashController::class, 'insert']);
    Route::post('/delete', [App\Http\Controllers\TrashController::class, 'delete'])->name('edits.delete');
    Route::post('/update', [App\Http\Controllers\TrashController::class, 'insert'])->name('edits.update');
});

Route::group(['namespace' => 'Shell', 'prefix' => 'shell', 'middleware' => 'auth:sanctum'], function (){
    Route::get('/status', [App\Http\Controllers\ShellController::class, 'status'])->name('shell.status');
    Route::get('/run', [App\Http\Controllers\ShellController::class, 'run'])->name('shell.run');
    Route::get('/update', [App\Http\Controllers\ShellController::class, 'update'])->name('shell.update');
});

Route::group(['namespace' => 'Product', 'prefix' => 'product', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Product\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Product\ProductController::class, 'update'])->name('product.update');
    Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('product.delete');
    Route::get('/geterrors', [App\Http\Controllers\Product\ProductController::class, 'geterrors'])->name('product.geterrors');
    Route::get('/getnotype', [App\Http\Controllers\Product\ProductController::class, 'getnotype'])->name('product.getnotype');
    Route::get('/getsmm', [App\Http\Controllers\Product\ProductController::class, 'getsmm'])->name('product.getsmm');
    Route::get('/monitor', [App\Http\Controllers\Product\ProductController::class, 'monitor'])->name('product.monitor');
    Route::post('/set', [App\Http\Controllers\Product\ProductController::class, 'set'])->name('product.set');
    Route::post('/feedid', [App\Http\Controllers\Product\ProductController::class, 'updateFeedID'])->name('product.feedid');
    Route::post('/setbytype', [App\Http\Controllers\Product\ProductController::class, 'setbytype'])->name('product.setbytype');
    Route::post('/settype', [App\Http\Controllers\Product\ProductController::class, 'settype'])->name('product.settype');
    Route::get('/stats', [App\Http\Controllers\Product\ProductController::class, 'stats'])->name('product.stats');
    Route::get('/countMonitor', [App\Http\Controllers\Product\ProductController::class, 'countMonitor'])->name('product.countMonitor');
    Route::post('/getSimilarType', [App\Http\Controllers\Product\ProductController::class, 'getSimilarType'])->name('product.getSimilarType');
    Route::post('/getSimilarTypeProducts', [App\Http\Controllers\Product\ProductController::class, 'getSimilarTypeProducts'])->name('product.getSimilarTypeProducts');
    Route::post('/updateSimilarType', [App\Http\Controllers\Product\ProductController::class, 'updateSimilarType'])->name('product.updateSimilarType');
    Route::post('/getSimilarTypeTrash', [App\Http\Controllers\Product\ProductController::class, 'getSimilarTypeTrash'])->name('product.getSimilarTypeTrash');
    Route::post('/addEdits', [App\Http\Controllers\Product\ProductController::class, 'addEdits'])->name('product.addEdits');
    Route::post('/addTrash', [App\Http\Controllers\Product\ProductController::class, 'addTrash'])->name('product.addTrash');
    Route::post('/trashSimilar', [App\Http\Controllers\Product\ProductController::class, 'trashSimilar'])->name('product.trashSimilar');
});

Route::group(['namespace' => 'Types', 'prefix' => 'types', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Type\TypeController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Type\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Type\TypeController::class, 'update'])->name('types.update');
    Route::post('/delete', [App\Http\Controllers\Type\TypeController::class, 'delete'])->name('types.delete');
    Route::get('/monitor', [App\Http\Controllers\Type\TypeController::class, 'monitor'])->name('types.monitor');
    Route::post('/setbytype', [App\Http\Controllers\Type\TypeController::class, 'setbytype'])->name('types.setbytype');
    Route::post('/commission', [App\Http\Controllers\Type\TypeController::class, 'saveCommission'])->name('types.commission');
});

Route::group(['namespace' => 'Colors', 'prefix' => 'colors', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Color\ColorController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Color\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Color\ColorController::class, 'update'])->name('colors.update');
    Route::post('/delete', [App\Http\Controllers\Color\ColorController::class, 'delete'])->name('colors.delete');
    Route::post('/check', [App\Http\Controllers\Color\ColorController::class, 'check'])->name('colors.check');
});

Route::group(['namespace' => 'Providers', 'prefix' => 'providers', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Provider\ProviderController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Provider\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Provider\ProviderController::class, 'update'])->name('providers.update');
    Route::post('/delete', [App\Http\Controllers\Provider\ProviderController::class, 'delete'])->name('providers.delete');
});

Route::group(['namespace' => 'Tax', 'prefix' => 'tax', 'middleware' => 'auth:sanctum'], function (){
    Route::get('/', [App\Http\Controllers\TaxController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\TaxController::class, 'update'])->name('providers.update');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
});
