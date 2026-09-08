<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('body');
            $table->enum('status', ['draft', 'published'])->default('draft')->after('meta_description');
        });
        DB::table('posts')->whereNotNull('published_at')->where('published_at', '<=', now())->update(['status' => 'published']);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'status']);
        });
    }
};
