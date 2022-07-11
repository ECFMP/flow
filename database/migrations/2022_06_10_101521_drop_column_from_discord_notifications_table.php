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
        Schema::table('discord_notifications', function (Blueprint $table) {
            $table->dropForeign('discord_notifications_flow_measure_id_foreign');
            $table->dropColumn(['flow_measure_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discord_notifications', function (Blueprint $table) {
            //
        });
    }
};
