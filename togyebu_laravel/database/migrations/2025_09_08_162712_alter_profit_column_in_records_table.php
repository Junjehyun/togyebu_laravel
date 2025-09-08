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
        Schema::table('records', function (Blueprint $table) {
            //
            // profit 컬럼을 bigInteger로 변경 (unsigned 제거)
            $table->bigInteger('profit')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            //
            // 원래 상태로 롤백 (unsignedBigInteger)
            $table->unsignedBigInteger('profit')->default(0)->change();
        });
    }
};
