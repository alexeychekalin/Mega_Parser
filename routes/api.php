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
});

Route::group(['namespace' => 'Log', 'prefix' => 'log', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\LogController::class, 'index']);
});

Route::group(['namespace' => 'Product', 'prefix' => 'product', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Product\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Product\ProductController::class, 'update'])->name('product.update');
    Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('product.delete');
    Route::post('/get99', [App\Http\Controllers\Product\ProductController::class, 'get99'])->name('product.get99');
});

Route::group(['namespace' => 'Types', 'prefix' => 'types', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Type\TypeController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Type\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Type\TypeController::class, 'update'])->name('types.update');
    Route::post('/delete', [App\Http\Controllers\Type\TypeController::class, 'delete'])->name('types.delete');
});

Route::group(['namespace' => 'Colors', 'prefix' => 'colors', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Color\ColorController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Color\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Color\ColorController::class, 'update'])->name('colors.update');
    Route::post('/delete', [App\Http\Controllers\Color\ColorController::class, 'delete'])->name('colors.delete');
});

Route::group(['namespace' => 'Providers', 'prefix' => 'providers', 'middleware' => 'auth:sanctum'], function (){
    Route::post('/', [App\Http\Controllers\Provider\ProviderController::class, 'index']);
    Route::get('/', [App\Http\Controllers\Provider\IndexController::class, 'index']);
    Route::post('/update', [App\Http\Controllers\Provider\ProviderController::class, 'update'])->name('providers.update');
    Route::post('/delete', [App\Http\Controllers\Provider\ProviderController::class, 'delete'])->name('providers.delete');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
});
