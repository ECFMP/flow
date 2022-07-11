<?php

use App\Models\FlightInformationRegion;
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
        Schema::create('flight_information_region_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignIdFor(FlightInformationRegion::class);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('flight_information_region_id', 'flight_information_region_id')
                ->references('id')
                ->on('flight_information_regions')
                ->cascadeOnDelete();
            $table->unique(['user_id', 'flight_information_region_id'], 'flight_information_region_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_information_region_user');
    }
};
