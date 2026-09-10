<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->timestamp('privacy_consent_at')->nullable()->after('notes');
            $table->string('privacy_consent_ip', 45)->nullable()->after('privacy_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['privacy_consent_at', 'privacy_consent_ip']);
        });
    }
};
