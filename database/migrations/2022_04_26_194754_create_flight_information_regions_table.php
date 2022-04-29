<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_information_regions', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 4)
                ->unique()
                ->comment('The FIR id, e.g. EGTT');
            $table->string('name')->comment('The name of the FIR, e.g. London');
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
        Schema::dropIfExists('flight_information_regions');
    }
};
