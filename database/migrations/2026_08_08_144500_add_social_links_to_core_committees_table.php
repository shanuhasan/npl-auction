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
        Schema::table('core_committees', function (Blueprint $table) {
            $table->string('facebook')->nullable()->after('role');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('linkedin')->nullable()->after('twitter');
            $table->string('whatsapp')->nullable()->after('linkedin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_committees', function (Blueprint $table) {
            $table->dropColumn(['facebook', 'instagram', 'twitter', 'linkedin', 'whatsapp']);
        });
    }
};
