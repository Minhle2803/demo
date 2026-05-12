<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   // references users.id
            $table->unsignedBigInteger('session_id');
            $table->enum('type', ['buy', 'sell']);
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['pending', 'win', 'lose'])->default('pending');
            $table->decimal('payout', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'session_id']);
            $table->index('session_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
