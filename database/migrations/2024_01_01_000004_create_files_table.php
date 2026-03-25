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
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('extension');
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained();
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_key')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['owner_id', 'status']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};