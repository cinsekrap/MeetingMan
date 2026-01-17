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
        Schema::table('people', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('linked_user_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->index('company_id');
            $table->index('linked_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['linked_user_id']);
            $table->dropColumn(['company_id', 'linked_user_id']);
        });
    }
};
