<?php

use App\Models\DivisionDiscordWebhook;
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
        Schema::create('division_discord_webhook_flight_information_region', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DivisionDiscordWebhook::class);
            $table->foreignIdFor(FlightInformationRegion::class);
            $table->timestamps();

            $table->unique(
                ['division_discord_webhook_id', 'flight_information_region_id'],
                'discord_webhook_fir_unique'
            );
            $table->foreign('division_discord_webhook_id', 'division_discord_fir_discord')
                ->references('id')
                ->on('division_discord_webhooks')
                ->cascadeOnDelete();
            $table->foreign('flight_information_region_id', 'division_discord_fir')
                ->references('id')
                ->on('flight_information_regions')
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
        Schema::dropIfExists('division_discord_webhook_flight_information_region');
    }
};
