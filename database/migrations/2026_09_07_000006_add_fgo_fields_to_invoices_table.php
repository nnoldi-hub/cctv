<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('fgo_id')->nullable()->after('invoice_number');
            $table->string('fgo_status')->nullable()->after('fgo_id');
            $table->dateTime('fgo_synced_at')->nullable()->after('fgo_status');
            $table->text('fgo_error')->nullable()->after('fgo_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['fgo_id', 'fgo_status', 'fgo_synced_at', 'fgo_error']);
        });
    }
};
