<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_cloudinary_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->text('failure_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_cloudinary_uploads');
    }
};