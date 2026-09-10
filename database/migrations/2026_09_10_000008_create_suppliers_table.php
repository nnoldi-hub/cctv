<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->decimal('markup_percent', 8, 2)->default(0)->after('cost_price');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'markup_percent']);
        });
        Schema::dropIfExists('suppliers');
    }
};
