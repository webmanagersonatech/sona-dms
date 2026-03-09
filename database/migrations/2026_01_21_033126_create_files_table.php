<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('file_uuid')->unique();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->string('original_name');
            $table->string('storage_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('extension');
            $table->enum('encryption_status', ['none', 'encrypted'])->default('encrypted');
            $table->string('encryption_key')->nullable();
            $table->json('permissions')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_shared')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};