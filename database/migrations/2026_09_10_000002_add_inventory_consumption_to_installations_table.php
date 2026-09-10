<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->json('material_items')->nullable()->after('materials');
            $table->dateTime('stock_consumed_at')->nullable()->after('material_items');
        });
    }

    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn(['material_items', 'stock_consumed_at']);
        });
    }
};
