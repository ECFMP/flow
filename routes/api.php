<?php

use App\Http\Controllers\DocumentationController;
use App\Http\Resources\FlowMeasureResource;
use App\Models\FlowMeasure;
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

// V1 routes
Route::middleware('guest')
    ->prefix('v1')
    ->group(function () {
        Route::prefix('flow-measure')
            ->group(function () {
                Route::get('', fn() => FlowMeasureResource::collection(FlowMeasure::all())->all());
                Route::get('{flowMeasure}', fn(int $id) => new FlowMeasureResource(FlowMeasure::findOrFail($id)));
            });
    });

// Documentation
Route::controller(DocumentationController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('v{number}', 'getDocumentationData')
            ->where(['number' => '\d+']);
    });

