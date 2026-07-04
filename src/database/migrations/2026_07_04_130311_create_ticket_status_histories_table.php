<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();
            $table->string('old_status', 32)
                ->nullable();
            $table->string('new_status', 32);
            $table->foreignId('changed_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
            $table->index(['changed_by', 'created_at']);
        });

        DB::statement(
            "ALTER TABLE ticket_status_histories
                ADD CONSTRAINT ticket_status_histories_old_status_check
                CHECK(
                    old_status IS NULL
                    OR old_status IN('new', 'in_progress', 'waiting_client', 'resolved', 'closed', 'cancelled')
                )"
        );

        DB::statement(
            "ALTER TABLE ticket_status_histories
                ADD CONSTRAINT ticket_status_histories_new_status_check
                CHECK(new_status IN('new', 'in_progress', 'waiting_client', 'resolved', 'closed', 'cancelled'))"
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_histories');
    }
};
