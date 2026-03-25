<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add missing columns to files table
        Schema::table('files', function (Blueprint $table) {
            if (!Schema::hasColumn('files', 'checksum')) {
                $table->string('checksum')->nullable()->after('encryption_key');
            }
            if (!Schema::hasColumn('files', 'version')) {
                $table->integer('version')->default(1)->after('checksum');
            }
            if (!Schema::hasColumn('files', 'tags')) {
                $table->json('tags')->nullable()->after('version');
            }
            if (!Schema::hasColumn('files', 'metadata')) {
                $table->json('metadata')->nullable()->after('tags');
            }
        });

        // Add missing columns to transfers table
        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'qr_code')) {
                $table->text('qr_code')->nullable()->after('signature');
            }
            if (!Schema::hasColumn('transfers', 'proof_of_delivery')) {
                $table->text('proof_of_delivery')->nullable()->after('qr_code');
            }
            if (!Schema::hasColumn('transfers', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable()->after('proof_of_delivery');
            }
            if (!Schema::hasColumn('transfers', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('cost');
            }
        });

        // Add missing columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('UTC')->after('settings');
            }
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('en')->after('timezone');
            }
            if (!Schema::hasColumn('users', 'email_verified')) {
                $table->boolean('email_verified')->default(false)->after('locale');
            }
            if (!Schema::hasColumn('users', 'phone_verified')) {
                $table->boolean('phone_verified')->default(false)->after('email_verified');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('phone_verified');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }
        });
    }

    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['checksum', 'version', 'tags', 'metadata']);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'proof_of_delivery', 'cost', 'currency']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'timezone', 'locale', 'email_verified', 'phone_verified',
                'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes'
            ]);
        });
    }
};