<?php

use App\Models\Event;
use App\Models\FlightInformationRegion;
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
        Schema::create('flow_measures', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')
                ->unique()
                ->comment('The identifier of the flow rule');
            $table->unsignedBigInteger('user_id')
                ->comment('The user who created this flow measure');
            $table->foreignIdFor(FlightInformationRegion::class)
                ->comment('The flight information region issuing this flow measure');
            $table->foreignIdFor(Event::class)
                ->nullable()
                ->comment('The event that this measure belongs to, if any');
            $table->text('reason')
                ->comment('The reason given for the flow measure being in place');
            $table->string('type')
                ->comment('The type of flow measure');
            $table->unsignedInteger('value')
                ->nullable()
                ->comment('Used to specify the value of the measure, for all but mandatory_route');
            $table->json('mandatory_route')
                ->nullable()
                ->comment('Used to specify mandatory route strings');
            $table->json('filters')
                ->comment('Any filters applied to the rule');
            $table->dateTime('start_time')
                ->comment('When the flow measure starts (Z)');
            $table->dateTime('end_time')
                ->comment('When the flow measure ends (Z)');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('flight_information_region_id')
                ->references('id')
                ->on('flight_information_regions');
            $table->foreign('event_id')->references('id')->on('events');
            $table->index(['start_time', 'end_time']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flow_measures');
    }
};
