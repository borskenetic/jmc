<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'profile_picture')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('profile_picture')
                ->where('profile_picture', '!=', '')
                ->where('profile_picture', 'not like', 'images/%')
                ->update([
                    'profile_picture' => DB::raw("CONCAT('images/profile_pictures/', profile_picture)"),
                ]);
        }
    }

    public function down(): void
    {
        // Irreversible: bare filenames cannot be recovered reliably.
    }
};
