<?php

use App\Models\FlightInformationRegion;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('The event name');
            $table->dateTime('date_start')->comment('When the event begins (Z)');
            $table->dateTime('date_end')->comment('When the event ends (Z)');
            $table->foreignIdFor(FlightInformationRegion::class);
            $table->string('vatcan_code')->nullable()->comment('The VATCAN events system code');
            $table->timestamps();

            $table->foreign('flight_information_region_id')->references('id')->on('flight_information_regions');
            $table->index(['date_start', 'date_end']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
