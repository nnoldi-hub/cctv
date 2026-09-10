<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->string('report_number')->nullable()->unique()->after('id');
            $table->dateTime('handover_at')->nullable()->after('completed_at');
            $table->string('technician_signature')->nullable()->after('handover_at');
            $table->string('customer_signature')->nullable()->after('technician_signature');
        });
    }

    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropUnique(['report_number']);
            $table->dropColumn([
                'report_number',
                'handover_at',
                'technician_signature',
                'customer_signature',
            ]);
        });
    }
};
