<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('division_discord_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500)
                ->comment('The webhook URL')
                ->unique('division_discord_webhooks_url_unique');
            $table->string('description')
                ->comment('What this webhook is for');
            $table->string('tag')
                ->nullable()
                ->comment('An optional tag to use to notify users of the target discord server');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('division_discord_webhooks');
    }
};
