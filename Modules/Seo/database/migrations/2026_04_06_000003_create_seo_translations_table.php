<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_entry_id')->constrained('seo_entries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('locale')->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots')->nullable();
            $table->string('canonical_url')->nullable();

            $table->unique(['seo_entry_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_translations');
    }
};
