<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE trading_sessions MODIFY COLUMN status ENUM('future','open','locked','closed') NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE trading_sessions MODIFY COLUMN status ENUM('open','locked','closed') NOT NULL DEFAULT 'open'");
    }
};
