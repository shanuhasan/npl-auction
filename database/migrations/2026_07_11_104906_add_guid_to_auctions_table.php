<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->uuid('guid')->nullable()->unique()->after('id');
        });

        // Populate existing auctions with a UUID
        $auctions = DB::table('auctions')->get();
        foreach ($auctions as $auction) {
            DB::table('auctions')->where('id', $auction->id)->update(['guid' => Str::uuid()->toString()]);
        }

        // Now make it not nullable
        Schema::table('auctions', function (Blueprint $table) {
            $table->uuid('guid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn('guid');
        });
    }
};
