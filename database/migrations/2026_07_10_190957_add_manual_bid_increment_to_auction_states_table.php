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
        Schema::table('auction_states', function (Blueprint $table) {
            $table->integer('manual_bid_increment')->default(0)->after('timer_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auction_states', function (Blueprint $table) {
            $table->dropColumn('manual_bid_increment');
        });
    }
};
