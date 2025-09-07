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
        Schema::create('records', function (Blueprint $table) {
            
            $table->id(); // PK
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // FK user 테이블과 연결
            
            $table->date('betting_date'); // 베팅 날짜
            
            $table->string('title'); // 내역, 제목
            
            $table->decimal('odds', 5, 2); // 배당
            
            $table->unsignedBigInteger('bet_amount'); // 베팅 금액
            
            $table->integer('folder_count'); // 폴더 수 
            
            $table->enum('result', ['win', 'lose', 'pending', 'draw'])->default('pending'); // 결과 상태 (승, 패, 진행중, 적특)
            
            $table->unsignedBigInteger('win_amount')->default(0); // 적중 금액
            
            $table->unsignedBigInteger('profit')->default(0); // 수익금(적중금)
            
            $table->timestamps(); // created_at, updated_at
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
