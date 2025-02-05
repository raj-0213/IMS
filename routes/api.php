<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DraftProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\MoleculesController;
use App\Http\Controllers\PublishedProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'index']);
        Route::post('/', [CategoriesController::class, 'store']);
        Route::get('/{id}', [CategoriesController::class, 'show']);
    });

    Route::prefix('molecules')->group(function () {
        Route::post('/', [MoleculesController::class, 'store']);
        Route::get('/', [MoleculesController::class, 'index']);
        Route::get('/{id}', [MoleculesController::class, 'show']);
        Route::put('/{id}', [MoleculesController::class, 'update']);
        Route::delete('/{id}', [MoleculesController::class, 'destroy']);
    });

    Route::prefix('drafts')->group(function () {
        Route::get('/', [DraftProductsController::class, 'index']);
        Route::post('/', [DraftProductsController::class, 'store']);
        Route::get('/{id}', [DraftProductsController::class, 'show']);
        Route::put('/{id}', [DraftProductsController::class, 'update']);
        Route::delete('/{id}', [DraftProductsController::class, 'destroy']);
    });


    Route::prefix('published')->group(function () {
        Route::get('/', [PublishedProductsController::class, 'index']);
        Route::get('/{id}', [PublishedProductsController::class, 'show']);
    });
});


// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/users', [UserController::class, 'index']);
//     Route::get('/users/{id}', [UserController::class, 'show']);
//     Route::put('/users/{id}', [UserController::class, 'update']);
//     Route::delete('/users/{id}', [UserController::class, 'destroy']);
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/allcategories', [CategoriesController::class, 'index']);
//     Route::post('/category', [CategoriesController::class, 'store']);
//     Route::get('/category/{id}', [CategoriesController::class, 'show']);
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/addmolecule', [MoleculesController::class, 'store']);
//     Route::get('/allmolecules', [MoleculesController::class, 'index']);
//     Route::get('/molecule/{id}', [MoleculesController::class, 'show']);
//     Route::put('/molecule/{id}', [MoleculesController::class, 'update']);
//     Route::delete('/molecule/{id}', [MoleculesController::class, 'destroy']);
// }); 

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/alldrafts', [DraftProductsController::class, 'index']);
//     Route::post('/draft', [DraftProductsController::class, 'store']);
//     Route::get('/draft/{id}', [DraftProductsController::class, 'show']);
//     Route::put('/draft/{id}', [DraftProductsController::class, 'update']);
    
//     Route::delete('/draft/{id}', [DraftProductsController::class, 'destroy']);
// });