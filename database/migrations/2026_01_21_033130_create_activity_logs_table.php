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

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('file_id')
                ->nullable()
                ->constrained('files')
                ->nullOnDelete();

            $table->foreignId('transfer_id')
                ->nullable()
                ->constrained('transfers')
                ->nullOnDelete();

            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('device_id')->nullable();
            $table->string('location')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->enum('action', [
                'login','logout',
                'file_upload','file_download','file_view','file_edit','file_delete',
                'file_share','share_access','share_revoke',
                'transfer_create','transfer_send','transfer_receive','transfer_cancel',
                'otp_sent','otp_verified',
                'third_party_access','access_revoke'
            ]);

            $table->text('description');
            $table->json('metadata')->nullable();

            $table->timestamp('performed_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
