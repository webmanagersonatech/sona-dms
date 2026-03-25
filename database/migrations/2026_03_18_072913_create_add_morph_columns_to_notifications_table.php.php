<?php
// database/migrations/YYYY_MM_DD_HHMMSS_add_morph_columns_to_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add missing polymorphic columns if they don't exist
            if (!Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->unsignedBigInteger('notifiable_id')->after('type');
            }
            
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->string('notifiable_type')->after('notifiable_id');
            }
            
            // Add index for polymorphic relationship
            $table->index(['notifiable_id', 'notifiable_type']);
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_id', 'notifiable_type']);
            $table->dropColumn(['notifiable_id', 'notifiable_type']);
        });
    }
};