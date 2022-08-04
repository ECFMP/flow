<?php

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
        Schema::table('division_discord_webhook_flight_information_region', function (Blueprint $table) {
            $table->string('tag')
                ->nullable()
                ->after('flight_information_region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('division_discord_webhook_flight_information_region', function (Blueprint $table) {
            //
        });
    }
};
