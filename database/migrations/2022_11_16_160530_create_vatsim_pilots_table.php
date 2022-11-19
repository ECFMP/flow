<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vatsim_pilots', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')
                ->comment('The pilot callsign')
                ->unique();
            $table->unsignedBigInteger('cid')
                ->comment('The users CID')
                ->index()
                ->unique();
            $table->string('departure_airport', 4)
                ->nullable()
                ->index();
            $table->string('destination_airport', 4)
                ->nullable()
                ->index();
            $table->mediumInteger('altitude');
            $table->unsignedMediumInteger('cruise_altitude')
                ->nullable();
            $table->text('route_string')
                ->nullable();
            $table->unsignedSmallInteger('vatsim_pilot_status_id')
                ->comment('The calculated flight status');
            $table->timestamp('estimated_arrival_time')
                ->nullable()
                ->comment('The calculated EAT');
            $table->float('distance_to_destination')
                ->nullable()
                ->comment('The calculated distance to destination');
            $table->timestamp('created_at')
                ->index();
            $table->timestamp('updated_at')
                ->index();

            $table->foreign('vatsim_pilot_status_id')->references('id')->on('vatsim_pilot_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vatsim_pilots');
    }
};
