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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('status', 32)->default('new');
            $table->string('priority', 32)->default('normal');

            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['client_id', 'created_at']);
            $table->index(['assigned_to', 'status', 'created_at']);
        });
        DB::statement(
            "ALTER TABLE tickets
             ADD CONSTRAINT tickets_status_check
             CHECK (status IN ('new', 'in_progress', 'waiting_client', 'resolved', 'closed', 'cancelled'))"
        );

        DB::statement(
            "ALTER TABLE tickets
             ADD CONSTRAINT tickets_priority_check
             CHECK (priority IN ('low', 'normal', 'high', 'urgent'))"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
