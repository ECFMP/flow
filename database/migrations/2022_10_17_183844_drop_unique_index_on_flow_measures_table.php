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
        Schema::table('flow_measures', function (Blueprint $table) {
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('flow_measures');
            if (array_key_exists('flow_measures_identifier_unique', $indexes)) {
                $table->dropUnique('flow_measures_identifier_unique');
            }

            $table->index('identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
