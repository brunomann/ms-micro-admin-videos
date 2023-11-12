<?php

use App\Http\Controllers\Api\{CastMemberController, CategoryController, GenreController};
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

Route::apiResource('/categories', CategoryController::class);
Route::apiResource(
    name: '/genres',
    controller: GenreController::class);
Route::apiResource(
    name: '/cast_members',
    controller: CastMemberController::class);

Route::get('/', function(){
    return response()->json(['message' => 'success']);
});
