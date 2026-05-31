<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Prevent duplicate recipients for the same message
            $table->unique(['message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};
