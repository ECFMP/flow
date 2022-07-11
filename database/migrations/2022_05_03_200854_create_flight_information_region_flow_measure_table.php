<?php

use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_information_region_flow_measure', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FlowMeasure::class)
                ->comment('The flow measure');
            $table->foreignIdFor(FlightInformationRegion::class)
                ->comment('The flight information region that needs to be concerned with the flow measure');
            $table->timestamps();

            $table->foreign('flow_measure_id', 'flight_information_region_flow_measure')
                ->references('id')
                ->on('flow_measures')
                ->cascadeOnDelete();

            $table->foreign('flight_information_region_id', 'flow_measure_flight_information_region')
                ->references('id')
                ->on('flight_information_regions')
                ->cascadeOnDelete();

            $table->unique(['flight_information_region_id', 'flow_measure_id'], 'fir_flow_measure_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_information_region_flow_measure');
    }
};
