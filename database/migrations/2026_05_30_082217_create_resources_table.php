<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('resource_type', ['file', 'external_link', 'video_link']);
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->enum('theme', ['education', 'health', 'environment', 'governance', 'agriculture', 'other'])->nullable();
            $table->year('year')->nullable();
            $table->enum('status', ['draft', 'submitted', 'needs_changes', 'approved', 'published', 'rejected'])->default('draft');
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};