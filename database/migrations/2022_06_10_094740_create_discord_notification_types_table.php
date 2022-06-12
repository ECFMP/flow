<?php

use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
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
        Schema::create('discord_notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamp('created_at');

            $table->unique('type');
        });

        DiscordNotificationType::create(
            [
                'type' => DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
            ]
        );
        DiscordNotificationType::create(
            [
                'type' => DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
            ]
        );
        DiscordNotificationType::create(
            [
                'type' => DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN,
            ]
        );
        DiscordNotificationType::create(
            [
                'type' => DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED,
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
        Schema::dropIfExists('discord_notification_types');
    }
};
