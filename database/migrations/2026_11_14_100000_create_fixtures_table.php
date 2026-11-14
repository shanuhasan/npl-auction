<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('match_no');
            $table->string('stage')->default('League'); // League, Qualifier, Semi Final, Final...
            $table->foreignId('team_a_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('team_b_id')->constrained('teams')->cascadeOnDelete();
            $table->dateTime('match_date')->nullable();
            $table->string('venue')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'abandoned'])->default('scheduled');
            $table->string('team_a_score')->nullable(); // e.g. "156/7 (20)"
            $table->string('team_b_score')->nullable();
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('result')->nullable(); // e.g. "Team A won by 5 wickets"
            $table->timestamps();

            $table->index(['auction_id', 'match_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
