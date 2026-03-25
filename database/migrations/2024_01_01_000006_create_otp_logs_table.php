<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('otp_code', 6);
            $table->string('purpose'); // login, file_access, third_party_access
            $table->foreignId('file_id')->nullable()->constrained();
            $table->foreignId('target_user_id')->nullable()->constrained('users');
            $table->string('target_email')->nullable();
            $table->datetime('expires_at');
            $table->datetime('verified_at')->nullable();
            $table->enum('status', ['pending', 'verified', 'expired'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_logs');
    }
};