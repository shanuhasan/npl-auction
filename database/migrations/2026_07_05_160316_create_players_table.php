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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->enum('role', ['batsman', 'bowler', 'all-rounder', 'wicketkeeper']);
            $table->string('country');
            $table->string('batting_style')->nullable();
            $table->string('bowling_style')->nullable();
            $table->decimal('base_price', 15, 2)->default(1000);
            $table->enum('category', ['marquee', 'set-a', 'set-b', 'set-c']);
            $table->json('stats')->nullable();
            $table->enum('status', ['available', 'sold', 'unsold'])->default('available');
            $table->foreignId('current_team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
