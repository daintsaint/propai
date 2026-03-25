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
        Schema::create('agent_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('agent_type'); // e.g., 'telegram_bot', 'email_autoresponder', 'crm_sync'
            $table->json('config_json'); // Agent-specific configuration
            $table->boolean('active')->default(true);
            $table->string('last_run_at')->nullable();
            $table->string('status')->default('idle'); // idle, running, error, paused
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_instances');
    }
};
