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
        Schema::create('users', function (Blueprint $table) {

            $table->id(); // 자동 증가 기본키 (Primary Key), 각 사용자 식별용

            //$table->string('account')->unique(); // 사용자 아이디 (로그인 계정, 고유값)

            $table->string('name'); // 사용자 이름 (닉네임이나 실명)

            $table->string('email')->unique(); // 이메일 (로그인 계정, 고유값)

            $table->timestamp('email_verified_at')->nullable(); // 이메일 인증 완료 시간

            $table->string('phone')->nullable(); // 전화번호

            $table->string('password'); // 로그인 비밀번호 (해시값 저장)

            $table->rememberToken(); // "로그인 상태 유지" 세션 토큰

            $table->enum('status', ['active', 'suspended', 'banned'])->default('active'); // 회원 계정 상태: active(활성), suspended(일시정지), banned(차단)

            $table->timestamp('last_login_at')->nullable(); // 마지막 로그인 시간 기록

            $table->string('login_ip')->nullable(); // 마지막 로그인 시 사용한 IP
            
            $table->integer('total_profit')->default(0); // 누적 수익금

            $table->text('note')->nullable(); // 관리자 메모 (특이사항 등 기록)

            $table->timestamps(); // created_at, updated_at 자동 관리
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
