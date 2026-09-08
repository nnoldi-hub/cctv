<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('location')->nullable()->after('description');
            $table->date('warranty_until')->nullable()->after('location');
            $table->date('installed_at')->nullable()->after('warranty_until');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn(['location', 'warranty_until', 'installed_at']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
