<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                // balance는 DB default(0) 또는 migration 기본값 사용
            ]);

            // ★ UserStat을 회원가입 시점에 반드시 생성 (이전에는 betConfirm에서만 lazy 생성 시도 → 절대 저장되지 않음)
            UserStat::create([
                'user_id' => $user->id,
                'betting_total_win' => 0,
                'betting_total_loss' => 0,
                'betting_total_draw' => 0,
            ]);

            return $user;
        });
    }
}
