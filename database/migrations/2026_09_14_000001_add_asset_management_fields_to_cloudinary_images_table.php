<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cloudinary_images', function (Blueprint $table) {
            $table->string('title')->nullable()->after('original_name');
            $table->text('description')->nullable()->after('title');
            $table->string('category')->nullable()->after('folder');
            $table->json('tags')->nullable()->after('category');
            $table->boolean('is_favorite')->default(false)->after('tags');
            $table->string('content_hash', 64)->nullable()->index()->after('is_favorite');
            $table->timestamp('deleted_at')->nullable()->index()->after('content_hash');
            $table->string('upload_status')->default('completed')->after('deleted_at');
            $table->unsignedInteger('retry_count')->default(0)->after('upload_status');
            $table->text('failure_message')->nullable()->after('retry_count');
        });
    }

    public function down(): void
    {
        Schema::table('cloudinary_images', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'description', 'category', 'tags', 'is_favorite',
                'content_hash', 'deleted_at', 'upload_status', 'retry_count',
                'failure_message',
            ]);
        });
    }
};