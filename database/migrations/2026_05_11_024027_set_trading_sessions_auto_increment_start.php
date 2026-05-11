<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE trading_sessions AUTO_INCREMENT = 100000');
    }

    public function down(): void
    {
        // No reverse — only affects future inserts.
    }
};
