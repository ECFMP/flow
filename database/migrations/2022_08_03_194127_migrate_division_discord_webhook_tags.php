<?php

use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migrate the existing tag values
        FlightInformationRegion::all()
            ->each(function (FlightInformationRegion $fir) {
                $fir->divisionDiscordWebhooks
                    ->each(function (DivisionDiscordWebhook $discordWebhook) use ($fir) {
                        $fir->divisionDiscordWebhooks()->updateExistingPivot(
                            $discordWebhook->id,
                            ['tag' => $discordWebhook->tag]
                        );
                    });
            });

        // Deduplicate them by URL
        DivisionDiscordWebhook::all()
            ->groupBy('url')
            ->reject(fn (Collection $webhooks) => $webhooks->count() === 1)
            ->each(function (Collection $webhooks) {
                // Get the one we're going to keep
                $webhookToKeep = $webhooks->shift();

                $webhooks->each(function (DivisionDiscordWebhook $webhook) use ($webhookToKeep) {
                    // Migrate the FIRs to the main webhook
                    $webhook->flightInformationRegions
                        ->each(function (FlightInformationRegion $flightInformationRegion) use ($webhook, $webhookToKeep) {
                            $flightInformationRegion->divisionDiscordWebhooks()
                                ->attach($webhookToKeep->id, ['tag' => $webhook->tag]);
                        });

                    // Delete the webhooks
                    $webhook->forceDelete();
                });
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
