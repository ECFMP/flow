<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discord_notification_flow_measure', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('discord_notification_id')
                ->constrained('discord_notifications', indexName: 'discord_notification_id')
                ->cascadeOnDelete();
            $table->foreignId('discord_notification_type_id')
                ->constrained('discord_notification_types', indexName: 'discord_notification_type_id')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discord_notification_flow_measure');
    }
};
