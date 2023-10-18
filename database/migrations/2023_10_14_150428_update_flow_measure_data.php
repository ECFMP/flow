<?php

use App\Helpers\FlowMeasureIdentifierGenerator;
use App\Models\FlowMeasure;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        FlowMeasure::all()->each(function (FlowMeasure $flowMeasure) {
            $flowMeasure->revision_number = FlowMeasureIdentifierGenerator::timesRevised($flowMeasure);
            $flowMeasure->canonical_identifier = FlowMeasureIdentifierGenerator::canonicalIdentifier($flowMeasure);
            $flowMeasure->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
