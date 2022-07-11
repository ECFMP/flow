<?php

use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FlowMeasureController;
use App\Http\Controllers\PluginApiController;
use App\Http\Resources\AirportGroupResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\AirportGroup;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
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
// V1 routes
Route::middleware('guest')
    ->prefix('v1')
    ->group(function () {
        Route::prefix('airport-group')
            ->group(function () {
                Route::get('', fn () => AirportGroupResource::collection(AirportGroup::all()));
                Route::get('{airportGroup}', fn (int $id) => new AirportGroupResource(AirportGroup::findOrFail($id)));
            });

        Route::prefix('event')
            ->group(function () {
                Route::get('', [EventController::class, 'getFilteredEvents']);
                Route::get('{event}', fn (int $id) => new EventResource(Event::findOrFail($id)));
            });

        Route::prefix('flow-measure')
            ->controller(FlowMeasureController::class)
            ->group(function () {
                Route::controller(FlowMeasureController::class)
                    ->get('', 'getFilteredFlowMeasures');
                Route::get('{flowMeasure}', fn (int $id) => new FlowMeasureResource(FlowMeasure::findOrFail($id)));
            });

        Route::prefix('flight-information-region')
            ->group(function () {
                Route::get('', fn () => FlightInformationRegionResource::collection(FlightInformationRegion::all()));
                Route::get(
                    '{flightInformationRegion}',
                    fn (int $id) => new FlightInformationRegionResource(FlightInformationRegion::findOrFail($id))
                );
            });

        Route::get('plugin', PluginApiController::class);
    });

// Documentation
Route::controller(DocumentationController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('v{number}', 'getDocumentationData')
            ->where(['number' => '\d+']);
    });
