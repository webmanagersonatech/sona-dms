<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('system_settings')->insert([
            ['key' => 'app_name', 'value' => 'Secure File Transfer'],
            ['key' => 'app_url', 'value' => config('app.url')],
            ['key' => 'file_upload_max_size', 'value' => '50'],
            ['key' => 'file_expiry_days', 'value' => '30'],
            ['key' => 'transfer_expiry_hours', 'value' => '72'],
            ['key' => 'otp_expiry_minutes', 'value' => '10'],
            ['key' => 'maintenance_mode', 'value' => '0'],
            ['key' => 'enable_registration', 'value' => '1'],
            ['key' => 'enable_two_factor', 'value' => '1'],
            ['key' => 'backup_interval', 'value' => 'daily'],
            ['created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};