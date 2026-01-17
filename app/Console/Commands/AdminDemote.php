<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AdminDemote extends Command
{
    protected $signature = 'admin:demote {email : The email address of the user to demote}';

    protected $description = 'Remove super admin privileges from a user';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        if (! $user->isSuperAdmin()) {
            $this->warn("{$user->name} ({$email}) is not a super admin.");
            return Command::SUCCESS;
        }

        $remainingAdmins = User::where('is_super_admin', true)
            ->where('id', '!=', $user->id)
            ->count();

        if ($remainingAdmins === 0) {
            $this->error('Cannot demote the last super admin. Promote another user first.');
            return Command::FAILURE;
        }

        $user->update(['is_super_admin' => false]);

        $this->info("✓ {$user->name} ({$email}) has been demoted from super admin.");

        return Command::SUCCESS;
    }
}
