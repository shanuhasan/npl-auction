<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('role');
        });
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'team_owner', 'viewer', 'sub_admin') DEFAULT 'viewer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'team_owner', 'viewer') DEFAULT 'viewer'");
    }
};
