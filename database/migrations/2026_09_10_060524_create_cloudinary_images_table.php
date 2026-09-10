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
        Schema::create('cloudinary_images', function (Blueprint $table) {
            $table->id();

            $table->string('original_name');
            $table->string('public_id')->unique();
            $table->string('secure_url', 2048);

            $table->string('format', 50)->nullable();
            $table->string('resource_type', 50)->default('image');

            $table->unsignedBigInteger('file_size')->default(0);

            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->string('folder')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloudinary_images');
    }
};