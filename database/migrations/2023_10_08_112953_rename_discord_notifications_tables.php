<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('discord_notifications', 'division_discord_notifications');
        Schema::rename('discord_notification_flow_measure', 'division_discord_notification_flow_measure');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
