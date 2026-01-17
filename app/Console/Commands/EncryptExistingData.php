<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\MeetingTopic;
use App\Models\Objective;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptExistingData extends Command
{
    protected $signature = 'security:encrypt-existing-data';

    protected $description = 'Encrypt existing unencrypted data in meeting_topics, actions, and objectives tables';

    public function handle(): int
    {
        $this->info('Starting encryption of existing data...');

        $this->encryptMeetingTopics();
        $this->encryptActions();
        $this->encryptObjectives();

        $this->info('Encryption complete!');

        return Command::SUCCESS;
    }

    private function encryptMeetingTopics(): void
    {
        $count = DB::table('meeting_topics')->count();
        $this->info("Encrypting {$count} meeting topics...");

        $bar = $this->output->createProgressBar($count);

        DB::table('meeting_topics')->orderBy('id')->chunk(100, function ($topics) use ($bar) {
            foreach ($topics as $topic) {
                // Skip if already encrypted (starts with eyJ - base64 encoded JSON)
                if ($topic->content && !str_starts_with($topic->content, 'eyJ')) {
                    MeetingTopic::withoutEvents(function () use ($topic) {
                        $model = MeetingTopic::find($topic->id);
                        // Force re-save to trigger encryption
                        DB::table('meeting_topics')
                            ->where('id', $topic->id)
                            ->update(['content' => encrypt($topic->content)]);
                    });
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function encryptActions(): void
    {
        $count = DB::table('actions')->count();
        $this->info("Encrypting {$count} actions...");

        $bar = $this->output->createProgressBar($count);

        DB::table('actions')->orderBy('id')->chunk(100, function ($actions) use ($bar) {
            foreach ($actions as $action) {
                if ($action->description && !str_starts_with($action->description, 'eyJ')) {
                    DB::table('actions')
                        ->where('id', $action->id)
                        ->update(['description' => encrypt($action->description)]);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function encryptObjectives(): void
    {
        $count = DB::table('objectives')->count();
        $this->info("Encrypting {$count} objectives...");

        $bar = $this->output->createProgressBar($count);

        DB::table('objectives')->orderBy('id')->chunk(100, function ($objectives) use ($bar) {
            foreach ($objectives as $objective) {
                if ($objective->definition && !str_starts_with($objective->definition, 'eyJ')) {
                    DB::table('objectives')
                        ->where('id', $objective->id)
                        ->update(['definition' => encrypt($objective->definition)]);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }
}
