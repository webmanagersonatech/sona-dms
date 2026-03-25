<?php
// database/migrations/2024_01_01_000011_add_settings_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'settings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('settings')->nullable()->after('status');
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};