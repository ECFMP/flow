<?php

use App\Models\DiscordTag;
use App\Models\FlightInformationRegion;
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
        Schema::create('discord_tag_flight_information_region', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FlightInformationRegion::class);
            $table->foreignIdFor(DiscordTag::class);
            $table->timestamps();

            $table->foreign('flight_information_region_id', 'discord_flight_information_region_id')
                ->references('id')
                ->on('flight_information_regions')
                ->cascadeOnDelete();

            $table->foreign('discord_tag_id', 'discord_discord_tag_id')
                ->references('id')
                ->on('discord_tags')
                ->cascadeOnDelete();

            $table->unique(['discord_tag_id', 'flight_information_region_id'], 'discord_tag_flight_information_region');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discord_tag_flight_information_region');
    }
};
