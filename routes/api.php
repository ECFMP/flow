<?php

use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FlowMeasureController;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\FlightInformationRegion;
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
            ->controller(FlowMeasureController::class)
            ->group(function () {
                Route::controller(FlowMeasureController::class)
                    ->get('', 'getFilteredFlowMeasures');
                Route::get('{flowMeasure}', fn(int $id) => new FlowMeasureResource(FlowMeasure::findOrFail($id)));
            });

        Route::prefix('flight-information-region')
            ->group(function () {
                Route::get('', fn() => FlightInformationRegionResource::collection(FlightInformationRegion::all()));
                Route::get(
                    '{flightInformationRegion}',
                    fn(int $id) => new FlightInformationRegionResource(FlightInformationRegion::findOrFail($id))
                );
            });
    });

// Documentation
Route::controller(DocumentationController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('v{number}', 'getDocumentationData')
            ->where(['number' => '\d+']);
    });

