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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->foreignId('assigned_to_person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->string('assigned_to_text')->nullable();
            $table->date('due_date');
            $table->enum('status', ['not_started', 'on_track', 'complete', 'dropped'])->default('not_started');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
