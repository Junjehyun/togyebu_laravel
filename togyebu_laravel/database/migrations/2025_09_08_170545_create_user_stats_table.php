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
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->unsignedInteger('betting_total_win')->default(0); // 베팅 승리 횟수
            $table->unsignedInteger('betting_total_loss')->default(0); // 베팅 패배 횟수
            $table->unsignedInteger('betting_total_draw')->default(0); // 베팅 무승부 횟수

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
