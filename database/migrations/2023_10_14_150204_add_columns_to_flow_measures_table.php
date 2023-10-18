<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flow_measures', function (Blueprint $table) {
            $table->string('canonical_identifier')
                ->after('identifier')
                ->comment('The original identifier of the flow measure');

            $table->unsignedInteger('revision_number')
                ->after('canonical_identifier')
                ->comment('The revision number of the flow measure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flow_measures', function (Blueprint $table) {
            $table->dropColumn('revision_number');
            $table->dropColumn('canonical_identifier');
        });
    }
};
