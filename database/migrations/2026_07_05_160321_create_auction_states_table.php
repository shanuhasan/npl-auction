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
        Schema::create('auction_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_auction_player_id')->nullable()->constrained('auction_players')->onDelete('set null');
            $table->decimal('current_highest_bid', 15, 2)->nullable();
            $table->foreignId('current_highest_team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->json('bid_increment_rule')->nullable();
            $table->integer('timer_seconds')->default(15);
            $table->timestamp('timer_end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_states');
    }
};
