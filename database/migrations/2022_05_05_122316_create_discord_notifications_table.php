<?php

use App\Models\FlowMeasure;
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
        Schema::create('discord_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FlowMeasure::class);
            $table->text('content');
            $table->timestamp('created_at');

            $table->foreign('flow_measure_id')
                ->references('id')
                ->on('flow_measures')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discord_notifications');
    }
};
