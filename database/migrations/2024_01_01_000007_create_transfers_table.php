<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_id')->unique();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->foreignId('file_id')->nullable()->constrained();
            $table->text('purpose');
            $table->text('description')->nullable();
            $table->datetime('expected_delivery_time');
            $table->datetime('actual_delivery_time')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'cancelled', 'failed'])->default('pending');
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();
            $table->text('delivery_location')->nullable();
            $table->text('received_by')->nullable();
            $table->text('signature')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['sender_id', 'status']);
            $table->index(['receiver_id', 'status']);
            $table->index(['transfer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfers');
    }
};