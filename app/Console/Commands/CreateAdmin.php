<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('admin:create {--update : Reset the password of an existing administrator}')]
#[Description('Create an administrator account (or reset its password) with interactive, hidden input')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $email = mb_strtolower(trim(text(
            label: 'Administrator email',
            required: true,
            validate: fn (string $value): ?string => filter_var(trim($value), FILTER_VALIDATE_EMAIL) ? null : 'Enter a valid email address.',
        )));

        $existing = User::where('email', $email)->first();

        if ($existing && ! $this->option('update')) {
            $this->components->error('An account with this email already exists. Use --update to reset its password.');

            return self::FAILURE;
        }

        if (! $existing && $this->option('update')) {
            $this->components->error('No account exists with this email.');

            return self::FAILURE;
        }

        $name = $existing?->name ?? text(label: 'Display name', default: 'Administrator', required: true);

        $plain = password(label: 'Password (min. 12 characters, letters and numbers)', required: true);
        $confirmation = password(label: 'Confirm password', required: true);

        $validator = Validator::make(
            ['password' => $plain, 'password_confirmation' => $confirmation],
            ['password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()]],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        if ($existing) {
            $existing->update(['password' => $plain, 'is_active' => true]);
            $this->components->info("Password updated for {$email}.");

            return self::SUCCESS;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $plain,
            'is_active' => true,
        ]);

        $this->components->info("Administrator {$email} created. Sign in at ".url('/admin/login'));

        return self::SUCCESS;
    }
}
