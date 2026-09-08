<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['call', 'email', 'meeting', 'visit', 'task', 'note'])->default('task');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('due_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'status']);
            $table->index(['assigned_to', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
