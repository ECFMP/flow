<?php

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
        Schema::create('discord_tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique()->comment('The tag to use');
            $table->string('description')->nullable()->comment('What the tag is for / who it is targeted at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discord_tags');
    }
};
