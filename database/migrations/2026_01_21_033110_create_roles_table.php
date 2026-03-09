<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'permissions' => json_encode(['*']), 'is_active' => true],
            ['name' => 'Admin', 'slug' => 'admin', 'permissions' => json_encode(['users.manage', 'files.view', 'transfers.manage']), 'is_active' => true],
            ['name' => 'Owner', 'slug' => 'owner', 'permissions' => json_encode(['files.manage', 'transfers.create', 'shares.manage']), 'is_active' => true],
            ['name' => 'Sender', 'slug' => 'sender', 'permissions' => json_encode(['files.upload', 'transfers.create']), 'is_active' => true],
            ['name' => 'Receiver', 'slug' => 'receiver', 'permissions' => json_encode(['files.view', 'transfers.receive']), 'is_active' => true],
            ['name' => 'Third-Party', 'slug' => 'third-party', 'permissions' => json_encode(['files.view']), 'is_active' => true],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};