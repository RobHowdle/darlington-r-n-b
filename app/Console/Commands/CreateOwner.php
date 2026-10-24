<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateOwner extends Command
{
    protected $signature = 'howdle:create-owner';

    protected $description = 'Create a verified CMS owner account interactively';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Run this command interactively to enter account details securely.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Name'));
        $email = strtolower(trim((string) $this->ask('Email')));
        $identity = Validator::make(compact('name', 'email'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ], ['email.unique' => 'An account with this email already exists.']);

        if ($identity->fails()) {
            foreach ($identity->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Password', fallback: false);
        $validation = Validator::make(['password' => $password], [
            'password' => ['required', 'string', Password::min(12)],
        ]);

        if ($validation->fails()) {
            $this->error('Password must contain at least 12 characters.');

            return self::FAILURE;
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $this->info('Owner account created. Sign in at /admin.');

        return self::SUCCESS;
    }
}
