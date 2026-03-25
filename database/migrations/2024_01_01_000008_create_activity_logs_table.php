<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('action'); // view, download, edit, print, share, upload, delete, archive, etc.
            $table->string('module'); // file, transfer, share, otp, user, department
            $table->foreignId('file_id')->nullable()->constrained();
            $table->foreignId('transfer_id')->nullable()->constrained();
            $table->text('description')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'module']);
            $table->index(['file_id', 'action']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};