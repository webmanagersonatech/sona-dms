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
            $table->uuid('transfer_uuid')->unique();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('file_id')->constrained('files');
            $table->enum('transfer_type', ['physical', 'cloud'])->default('physical');
            $table->text('purpose');
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'received', 'cancelled'])->default('pending');
            $table->timestamp('expected_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->string('delivery_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('third_party_involved')->default(false);
            $table->string('third_party_name')->nullable();
            $table->string('third_party_email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfers');
    }
};