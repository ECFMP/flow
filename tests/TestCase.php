<?php

namespace Tests;

use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function beforeRefreshingDatabase()
    {
        DB::table('discord_notifications')->delete();
        DB::table('division_discord_webhooks')->delete();
        DB::table('flow_measures')->delete();
        DB::table('events')->delete();
        DB::table('flight_information_regions')->delete();
        DB::table('users')->delete();
        DB::table('airport_groups')->delete();
        DB::table('airports')->delete();
        DB::table('discord_tags')->delete();
        DB::table('vatsim_pilots')->delete();
    }
}
