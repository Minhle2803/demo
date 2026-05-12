<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('admin:create')]
#[Description('Create a new admin account')]
class CreateAdminCommand extends Command
{
    public function handle(): int
    {
        $name = $this->ask('Tên admin', 'Admin');
        $email = $this->ask('Email');
        $password = $this->secret('Mật khẩu');

        if (empty($email)) {
            $this->error('Email không được để trống.');

            return self::FAILURE;
        }

        if (empty($password)) {
            $this->error('Mật khẩu không được để trống.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Email \"{$email}\" đã tồn tại.");

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info("Admin \"{$user->name}\" <{$user->email}> đã được tạo thành công.");

        return self::SUCCESS;
    }
}
