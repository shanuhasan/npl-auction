<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Playoff matches (Semi Final / Final) are scheduled before the teams are known:
 * the team columns become nullable and a label ("1st in League", "Winner SF 1")
 * is shown until a team is filled in. No team and no label = "TBC".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->unsignedBigInteger('team_a_id')->nullable()->change();
            $table->unsignedBigInteger('team_b_id')->nullable()->change();
            $table->string('team_a_label')->nullable()->after('team_a_id');
            $table->string('team_b_label')->nullable()->after('team_b_id');
        });
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('fixtures')->whereNull('team_a_id')->orWhereNull('team_b_id')->delete();

        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn(['team_a_label', 'team_b_label']);
            $table->unsignedBigInteger('team_a_id')->nullable(false)->change();
            $table->unsignedBigInteger('team_b_id')->nullable(false)->change();
        });
    }
};
