<?php

use App\Http\Controllers\FlowMeasureController;
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

Route::controller(FlowMeasureController::class)
    ->prefix('flow-measure')
    ->group(function () {
        Route::get('', 'getAllFlowMeasures');
        Route::get('{flowMeasure}', 'getFlowMeasure');
    });
