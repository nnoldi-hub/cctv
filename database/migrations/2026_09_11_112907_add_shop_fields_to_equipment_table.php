<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->boolean('is_visible_in_shop')->default(false)->after('is_active');
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('image_path')->nullable()->after('slug');
            $table->text('shop_description')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['is_visible_in_shop', 'slug', 'image_path', 'shop_description']);
        });
    }
};
