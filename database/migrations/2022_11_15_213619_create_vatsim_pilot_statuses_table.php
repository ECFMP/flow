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
        Schema::create('vatsim_pilot_statuses', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('description');
        });

        DB::table('vatsim_pilot_statuses')
            ->insert(
                [
                    [
                        'id' => 1,
                        'description' => 'Ground',
                    ],
                    [
                        'id' => 2,
                        'description' => 'Departing',
                    ],
                    [
                        'id' => 3,
                        'description' => 'Cruise',
                    ],
                    [
                        'id' => 4,
                        'description' => 'Descending',
                    ],
                    [
                        'id' => 5,
                        'description' => 'Landed',
                    ],
                ]
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vatsim_pilot_statuses');
    }
};
