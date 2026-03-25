<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp_code', 10);
            $table->enum('purpose', ['login', 'file_access', 'transfer_approval', 'third_party_access']);
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('file_id')->nullable()->constrained('files');
            $table->foreignId('transfer_id')->nullable()->constrained('transfers');
            $table->string('device_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('otps');
    }
};