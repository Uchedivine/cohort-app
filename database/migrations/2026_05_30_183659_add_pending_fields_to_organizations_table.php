<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new columns
        Schema::table('organizations', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('applied_at')->nullable()->after('rejection_reason');
        });

        // Step 2: Modify the status enum to include 'pending'
        DB::statement("ALTER TABLE organizations MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'submitted',
            'needs_changes',
            'approved',
            'published',
            'rejected'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'applied_at']);
        });

        DB::statement("ALTER TABLE organizations MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'needs_changes',
            'approved',
            'published',
            'rejected'
        ) NOT NULL DEFAULT 'draft'");
    }
};