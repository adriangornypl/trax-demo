<?php

use App\Http\Controllers\CarsController;
use App\Http\Controllers\TripsController;
use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//////////////////////////////////////////////////////////////////////////
/// Mock Endpoints To Be Replaced With RESTful API.
/// - API implementation needs to return data in the format seen below.
/// - Post data will be in the format seen below.
/// - /resource/assets/traxAPI.js will have to be updated to align with
///   the API implementation
//////////////////////////////////////////////////////////////////////////

Route::get('/cars', [CarsController::class, 'index']);
Route::post('/cars', [CarsController::class, 'create']);
Route::delete('/cars/{car}', [CarsController::class, 'remove']);

Route::get('/trips', [TripsController::class, 'index']);
Route::post('/trips', [TripsController::class, 'create']);
Route::delete('/trips/{trip}', [TripsController::class, 'remove']);
