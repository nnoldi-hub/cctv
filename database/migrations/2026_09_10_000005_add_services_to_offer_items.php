<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_items', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('equipment_id')->constrained()->nullOnDelete();
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->json('service_items')->nullable()->after('material_items');
        });
    }

    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn('service_items');
        });

        Schema::table('offer_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
