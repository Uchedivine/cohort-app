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
        Schema::table('submissions', function (Blueprint $table) {
            // Allow secretary to enable resubmission for rejected submissions
            $table->boolean('allow_resubmission')->default(false)->after('reviewer_notes');
            
            // Link resubmitted content to original rejected submission
            $table->unsignedBigInteger('parent_submission_id')->nullable()->after('allow_resubmission');
            $table->foreign('parent_submission_id')
                ->references('id')
                ->on('submissions')
                ->onDelete('set null');
            
            $table->index('parent_submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['parent_submission_id']);
            $table->dropIndex(['parent_submission_id']);
            $table->dropColumn(['allow_resubmission', 'parent_submission_id']);
        });
    }
};
