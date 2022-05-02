<?php

use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FlowMeasureController;
use App\Http\Resources\EventResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\Event;
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
        Route::prefix('event')
            ->group(function () {
                Route::get('', [EventController::class, 'getFilteredEvents']);
                Route::get('{event}', fn(int $id) => new EventResource(Event::findOrFail($id)));
            });

        Route::prefix('flow-measure')
            ->controller(FlowMeasureController::class)
            ->group(function () {
                Route::controller(FlowMeasureController::class)
                    ->get('', 'getFilteredFlowMeasures');
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

