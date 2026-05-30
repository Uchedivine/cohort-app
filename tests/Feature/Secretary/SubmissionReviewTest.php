<?php

namespace Tests\Feature\Secretary;

use App\Models\Organization;
use App\Models\Story;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $secretary;
    private User $orgEditor;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        
        $this->secretary = User::factory()->create();
        $this->secretary->assignRole('secretary');

        $this->orgEditor = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $this->orgEditor->assignRole('org_editor');
    }

    /** @test */
    public function secretary_can_view_submissions_index()
    {
        $response = $this->actingAs($this->secretary)
            ->get(route('secretary.submissions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('secretary.submissions.index');
    }

    /** @test */
    public function secretary_can_view_submission_details()
    {
        $story = Story::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->orgEditor->id,
        ]);

        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $this->orgEditor->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->secretary)
            ->get(route('secretary.submissions.show', $submission));

        $response->assertStatus(200);
        $response->assertViewIs('secretary.submissions.show');
    }

    /** @test */
    public function secretary_can_approve_submission()
    {
        $story = Story::factory()->create([
            'status' => 'submitted',
        ]);

        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->secretary)
            ->post(route('secretary.submissions.approve', $submission));

        $response->assertRedirect(route('secretary.submissions.index'));
        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals('approved', $submission->status);
        $this->assertEquals($this->secretary->id, $submission->reviewer_id);

        $story->refresh();
        $this->assertEquals('published', $story->status);
    }

    /** @test */
    public function secretary_can_reject_submission()
    {
        $story = Story::factory()->create();
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->secretary)
            ->post(route('secretary.submissions.reject', $submission), [
                'reviewer_notes' => 'Content does not meet guidelines',
            ]);

        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals('rejected', $submission->status);
        $this->assertEquals('Content does not meet guidelines', $submission->reviewer_notes);
    }

    /** @test */
    public function secretary_can_request_changes()
    {
        $story = Story::factory()->create();
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->secretary)
            ->post(route('secretary.submissions.request-changes', $submission), [
                'reviewer_notes' => 'Please add more details about the outcome',
            ]);

        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals('needs_changes', $submission->status);
    }

    /** @test */
    public function rejection_requires_reviewer_notes()
    {
        $submission = Submission::factory()->create(['status' => 'submitted']);

        $response = $this->actingAs($this->secretary)
            ->post(route('secretary.submissions.reject', $submission), [
                'reviewer_notes' => '',
            ]);

        $response->assertSessionHasErrors('reviewer_notes');
    }

    /** @test */
    public function org_editor_cannot_access_submission_review()
    {
        $submission = Submission::factory()->create();

        $response = $this->actingAs($this->orgEditor)
            ->get(route('secretary.submissions.index'));

        $response->assertStatus(403);
    }
}
