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
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('seed_key', 100)->nullable()->unique();
            $table->string('page', 20)->index();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('heading_ar');
            $table->string('heading_en')->nullable();
            $table->text('text_ar')->nullable();
            $table->text('text_en')->nullable();
            $table->string('cta1_label_ar', 60)->nullable();
            $table->string('cta1_label_en', 60)->nullable();
            $table->string('cta1_target', 30)->nullable();
            $table->string('cta2_label_ar', 60)->nullable();
            $table->string('cta2_label_en', 60)->nullable();
            $table->string('cta2_target', 30)->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
