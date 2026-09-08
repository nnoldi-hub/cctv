<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('pipeline_stage')->default('new')->after('status');
            $table->string('lost_reason')->nullable()->after('pipeline_stage');
            $table->index(['pipeline_stage', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['pipeline_stage', 'assigned_to']);
            $table->dropColumn(['pipeline_stage', 'lost_reason']);
        });
    }
};
