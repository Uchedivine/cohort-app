<?php

namespace Tests\Unit\Services;

use App\Models\Story;
use App\Models\Submission;
use App\Models\User;
use App\Models\Organization;
use App\Services\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubmissionService $service;
    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new SubmissionService();
        
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
    }

    /** @test */
    public function it_can_create_a_submission()
    {
        $story = Story::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
        ]);

        $submission = $this->service->createSubmission($story, $this->user, 'draft');

        $this->assertInstanceOf(Submission::class, $submission);
        $this->assertEquals('draft', $submission->status);
        $this->assertEquals($story->id, $submission->submittable_id);
        $this->assertEquals($this->user->id, $submission->submitted_by);
    }

    /** @test */
    public function it_can_submit_a_draft()
    {
        $story = Story::factory()->create();
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $updated = $this->service->submit($submission);

        $this->assertEquals('submitted', $updated->status);
        $this->assertNotNull($updated->submitted_at);
    }

    /** @test */
    public function it_can_approve_a_submission()
    {
        $story = Story::factory()->create(['status' => 'submitted']);
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'status' => 'submitted',
        ]);

        $reviewer = User::factory()->create();
        $approved = $this->service->approve($submission, $reviewer, 'Looks good!');

        $this->assertEquals('approved', $approved->status);
        $this->assertEquals($reviewer->id, $approved->reviewer_id);
        $this->assertNotNull($approved->reviewed_at);
        
        $story->refresh();
        $this->assertEquals('published', $story->status);
    }

    /** @test */
    public function it_can_reject_a_submission()
    {
        $story = Story::factory()->create();
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'status' => 'submitted',
        ]);

        $reviewer = User::factory()->create();
        $rejected = $this->service->reject($submission, $reviewer, 'Needs improvement');

        $this->assertEquals('rejected', $rejected->status);
        $this->assertEquals('Needs improvement', $rejected->reviewer_notes);
    }

    /** @test */
    public function it_can_request_changes()
    {
        $story = Story::factory()->create();
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'status' => 'submitted',
        ]);

        $reviewer = User::factory()->create();
        $updated = $this->service->requestChanges($submission, $reviewer, 'Please add more details');

        $this->assertEquals('needs_changes', $updated->status);
        $this->assertEquals('Please add more details', $updated->reviewer_notes);
    }

    /** @test */
    public function it_validates_status_transitions()
    {
        $this->expectException(\InvalidArgumentException::class);

        $submission = Submission::factory()->create(['status' => 'approved']);
        $reviewer = User::factory()->create();
        
        // Cannot reject an already approved submission
        $this->service->reject($submission, $reviewer, 'Too late');
    }

    /** @test */
    public function it_can_get_pending_submissions()
    {
        Submission::factory()->count(3)->create(['status' => 'submitted']);
        Submission::factory()->count(2)->create(['status' => 'approved']);

        $pending = $this->service->getPendingSubmissions();

        $this->assertCount(3, $pending);
    }

    /** @test */
    public function it_can_check_if_submission_can_be_edited()
    {
        $draft = Submission::factory()->create(['status' => 'draft']);
        $needsChanges = Submission::factory()->create(['status' => 'needs_changes']);
        $approved = Submission::factory()->create(['status' => 'approved']);

        $this->assertTrue($this->service->canEdit($draft));
        $this->assertTrue($this->service->canEdit($needsChanges));
        $this->assertFalse($this->service->canEdit($approved));
    }
}
