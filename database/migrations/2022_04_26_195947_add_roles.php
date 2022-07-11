<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(
            [
                'key' => 'SYSTEM',
                'description' => 'System user'
            ]
        );

        Role::create(
            [
                'key' => 'NMT',
                'description' => 'Network Management Team'
            ]
        );

        Role::create(
            [
                'key' => 'FLOW_MANAGER',
                'description' => 'Flow Manager'
            ]
        );

        Role::create(
            [
                'key' => 'USER',
                'description' => 'Normal User - View Only'
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
        //
    }
};
