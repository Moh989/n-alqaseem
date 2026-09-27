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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('seed_key', 100)->nullable()->unique();
            $table->string('directory', 191);
            $table->string('original_name')->nullable();
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedBigInteger('bytes')->default(0);
            $table->json('widths');
            $table->string('alt_ar')->nullable();
            $table->string('alt_en')->nullable();
            $table->boolean('is_stock')->default(false);
            $table->string('credit')->nullable();
            $table->string('source_url', 500)->nullable();
            $table->string('license', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
