<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->uuid('guid')->nullable()->unique()->after('id');
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Backfill existing teams
        $used = [];
        foreach (DB::table('teams')->orderBy('id')->get(['id', 'name']) as $team) {
            $base = Str::slug($team->name) ?: 'team';
            $slug = $base;
            $i = 2;
            while (in_array($slug, $used, true)) {
                $slug = $base . '-' . $i++;
            }
            $used[] = $slug;

            DB::table('teams')->where('id', $team->id)->update([
                'guid' => (string) Str::uuid(),
                'slug' => $slug,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['guid']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['guid', 'slug']);
        });
    }
};
