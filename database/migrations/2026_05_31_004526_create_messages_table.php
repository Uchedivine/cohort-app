<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('body');
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade');
            $table->enum('recipient_type', ['all', 'multiple', 'single'])->default('single');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
