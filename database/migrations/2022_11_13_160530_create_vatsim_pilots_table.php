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
            $table->id(); #
            $table->string('callsign')
                ->comment('The pilot callsign')
                ->unique();
            $table->unsignedBigInteger('cid')
                ->comment('The users CID')
                ->index()
                ->unique();
            $table->string('departure_airport', 4)
                ->index();
            $table->string('destination_airport', 4)
                ->index();
            $table->unsignedMediumInteger('flight_level');
            $table->unsignedMediumInteger('cruise_level');
            $table->string('route_string', 1500);
            $table->timestamps();
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
