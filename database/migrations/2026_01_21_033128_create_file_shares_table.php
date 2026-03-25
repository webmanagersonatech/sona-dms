<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('file_shares', function (Blueprint $table) {
    $table->id();
    $table->uuid('share_token')->unique();

    $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();
    $table->foreignId('shared_by')->constrained('users')->cascadeOnDelete();
    $table->foreignId('shared_with')->nullable()->constrained('users')->nullOnDelete();

    $table->string('shared_email')->nullable();
    $table->json('permissions')->nullable();

    $table->timestamp('valid_from')->nullable();
    $table->timestamp('valid_until')->nullable(); // ✅ FIX

    $table->integer('max_access_count')->nullable();
    $table->integer('access_count')->default(0);

    $table->boolean('requires_otp_approval')->default(true);
    $table->boolean('is_active')->default(true);

    $table->timestamp('last_accessed_at')->nullable();
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('file_shares');
    }
};