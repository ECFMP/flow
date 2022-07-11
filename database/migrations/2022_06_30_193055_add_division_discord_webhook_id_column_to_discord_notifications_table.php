<?php

use App\Models\DivisionDiscordWebhook;
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
        Schema::table('discord_notifications', function (Blueprint $table) {
            $table->foreignIdFor(DivisionDiscordWebhook::class)
                ->after('id')
                ->nullable()
                ->comment('Which divisional discord server this notification was sent to');

            $table->foreign('division_discord_webhook_id', 'discord_notifications_webhook')
                ->references('id')
                ->on('division_discord_webhooks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discord_notifications', function (Blueprint $table) {
            $table->dropForeign('division_discord_webhook_id');
            $table->dropColumn('division_discord_webhook_id');
        });
    }
};
