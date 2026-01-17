<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AdminPromote extends Command
{
    protected $signature = 'admin:promote {email : The email address of the user to promote}';

    protected $description = 'Promote a user to super admin';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        if ($user->isSuperAdmin()) {
            $this->warn("{$user->name} ({$email}) is already a super admin.");
            return Command::SUCCESS;
        }

        $user->update(['is_super_admin' => true]);

        $this->info("✓ {$user->name} ({$email}) has been promoted to super admin.");

        return Command::SUCCESS;
    }
}
