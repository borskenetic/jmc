<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('employees', 'rfid')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->string('rfid')->nullable()->unique()->after('qrcode');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('employees', 'rfid')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['rfid']);
            $table->dropColumn('rfid');
        });
    }
};
