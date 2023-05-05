<?php

use App\Http\Controllers\API\TarifaController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\AdminController;
use App\Models\role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::post('logout', [UserController::class, 'logout'])->middleware(['auth:sanctum']);
// Route::group(['prefix' => 'posts', 'middleware' => 'auth:sanctum'], function () {
//     Route::get('/', [PostController::class, 'index']);
//     Route::post('add', [PostController::class, 'add']);
// });
Route::get('tarifas', [TarifaController::class, 'index']);
Route::post('tarifasSelect', [TarifaController::class, 'selectTarifas']);
Route::post('userSelect', [UserController::class, 'selectUser']);
Route::post('rolesSelect', [RoleController::class, 'selectRoles']);
Route::get('roles', [RoleController::class, 'allRoles']);
Route::post('createUsers', [AdminController::class, 'addUser']);
Route::get('users', [AdminController::class, 'allUsers']);
Route::delete('deleteUsers/{id}', [AdminController::class, 'delete']);
Route::post('update/{id}', [AdminController::class, 'updateUsers']);