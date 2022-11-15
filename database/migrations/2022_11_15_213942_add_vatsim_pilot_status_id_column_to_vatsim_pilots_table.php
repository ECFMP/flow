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
        Schema::table('vatsim_pilots', function (Blueprint $table) {
            $table->unsignedSmallInteger('vatsim_pilot_status_id')
                ->default(2);
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
        Schema::table('vatsim_pilots', function (Blueprint $table) {
            $table->dropForeign('vatsim_pilots_vatsim_pilot_status_id_foreign');
            $table->dropColumn('vatsim_pilot_status_id');
        });
    }
};
