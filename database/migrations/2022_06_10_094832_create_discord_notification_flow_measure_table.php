<?php

use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
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
        Schema::create('discord_notification_flow_measure', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DiscordNotification::class);
            $table->foreignIdFor(FlowMeasure::class);
            $table->foreignIdFor(DiscordNotificationType::class);
            $table->string('notified_as')
                ->comment(
                    'What the identifier of the flow measure was at the time the discord notification was sent'
                );
            $table->timestamps();

            $table->foreign('discord_notification_id', 'discord_flow_measure_discord')
                ->references('id')
                ->on('discord_notifications')
                ->cascadeOnDelete();

            $table->foreign('flow_measure_id', 'discord_flow_measure_flow')
                ->references('id')
                ->on('flow_measures')
                ->cascadeOnDelete();

            $table->foreign('discord_notification_type_id', 'discord_flow_measure_type')
                ->references('id')
                ->on('discord_notification_types')
                ->cascadeOnDelete();
        });

        DiscordNotification::all()->each(function (DiscordNotification $discordNotification) {
            \Illuminate\Support\Facades\DB::table('discord_notification_flow_measure')
                ->insert(
                    [
                        'discord_notification_id' => $discordNotification->id,
                        'flow_measure_id' => $discordNotification->flow_measure_id,
                        'discord_notification_type_id' => DiscordNotificationType::where(
                            'type',
                            $discordNotification->type
                        )
                            ->firstOrFail()->id,
                        'notified_as' => FlowMeasure::findOrFail($discordNotification->flow_measure_id)->identifier,
                        'created_at' => $discordNotification->created_at,
                    ]
                );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discord_notification_flow_measure');
    }
};
