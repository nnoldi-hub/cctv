<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->decimal('labor_hours', 8, 2)->nullable()->after('scheduled_at');
            $table->json('materials')->nullable()->after('checklist');
            $table->json('photos')->nullable()->after('materials');
            $table->string('customer_name')->nullable()->after('photos');
            $table->text('customer_notes')->nullable()->after('customer_name');
            $table->dateTime('completed_at')->nullable()->after('customer_notes');
        });
    }

    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn([
                'labor_hours', 'materials', 'photos', 'customer_name',
                'customer_notes', 'completed_at',
            ]);
        });
    }
};
