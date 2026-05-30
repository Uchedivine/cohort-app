<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->text('summary')->nullable();
            $table->longText('full_story')->nullable();
            $table->string('author')->nullable();
            $table->json('sdgs')->nullable();
            $table->text('problem')->nullable();
            $table->text('approach')->nullable();
            $table->text('outcome')->nullable();
            $table->text('lessons')->nullable();
            $table->enum('status', ['draft', 'submitted', 'needs_changes', 'approved', 'published', 'rejected'])->default('draft');
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};