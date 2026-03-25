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
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_by')->constrained('users');
            $table->foreignId('shared_with')->constrained('users');
            $table->enum('permission_level', ['view', 'download', 'edit', 'print', 'full_control'])->default('view');
            $table->datetime('expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->text('access_token')->nullable();
            $table->timestamps();
            
            $table->index(['file_id', 'shared_with', 'status']);
            $table->index(['access_token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_shares');
    }
};