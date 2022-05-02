<?php

use App\Models\Airport;
use App\Models\AirportGroup;
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
        Schema::create('airport_airport_group', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Airport::class);
            $table->foreignIdFor(AirportGroup::class);
            $table->timestamps();

            $table->foreign('airport_id')->references('id')->on('airports')->cascadeOnDelete();
            $table->foreign('airport_group_id')->references('id')->on('airport_groups')->cascadeOnDelete();
            $table->unique(['airport_id', 'airport_group_id'], 'airport_airport_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airport_airport_group');
    }
};
