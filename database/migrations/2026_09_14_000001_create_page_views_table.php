<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('visitor_id', 100)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('url', 2048);
            $table->string('path', 255)->index();
            $table->string('page_title', 255)->nullable();
            $table->string('method', 10)->default('GET');
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type', 20)->default('desktop')->index();
            $table->string('browser', 50)->nullable();
            $table->string('platform', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->string('referer_domain', 255)->nullable()->index();
            $table->string('utm_source', 100)->nullable()->index();
            $table->string('utm_medium', 100)->nullable()->index();
            $table->string('utm_campaign', 100)->nullable()->index();
            $table->string('utm_content', 100)->nullable();
            $table->boolean('is_bot')->default(false)->index();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
