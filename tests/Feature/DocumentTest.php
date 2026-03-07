<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    // ───────── Index ─────────

    public function test_documents_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.documents'));
        $response->assertStatus(200);
    }

    public function test_documents_page_requires_authentication(): void
    {
        $response = $this->get(route('dashboard.documents'));
        $response->assertRedirect('/login');
    }

    // ───────── Upload ─────────

    public function test_user_can_upload_pdf_document(): void
    {
        $file = UploadedFile::fake()->create('permis.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'type' => 'driving_license',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_upload_jpg_image(): void
    {
        $file = UploadedFile::fake()->image('identity.jpg', 800, 600);

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'identity_card',
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'type' => 'identity_card',
        ]);
    }

    public function test_user_can_upload_png_image(): void
    {
        $file = UploadedFile::fake()->image('passport.png', 600, 400);

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'passport',
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
    }

    public function test_upload_fails_with_invalid_file_type(): void
    {
        $file = UploadedFile::fake()->create('malware.exe', 500, 'application/x-msdownload');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('documents', ['user_id' => $this->user->id]);
    }

    public function test_upload_fails_with_csv_file(): void
    {
        $file = UploadedFile::fake()->create('data.csv', 200, 'text/csv');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_fails_with_oversized_file(): void
    {
        $file = UploadedFile::fake()->create('big.pdf', 6000, 'application/pdf'); // 6MB > 5MB limit

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_fails_with_invalid_document_type(): void
    {
        $file = UploadedFile::fake()->create('doc.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'invalid_type',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_upload_fails_without_file(): void
    {
        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_fails_without_type(): void
    {
        $file = UploadedFile::fake()->create('doc.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_upload_with_expiry_date(): void
    {
        $file = UploadedFile::fake()->create('permis.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
            'expiry_date' => today()->addYear()->format('Y-m-d'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('documents', [
            'user_id' => $this->user->id,
            'type' => 'driving_license',
        ]);
    }

    public function test_upload_fails_with_past_expiry_date(): void
    {
        $file = UploadedFile::fake()->create('permis.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
            'expiry_date' => today()->subDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('expiry_date');
    }

    // ───────── Delete ─────────

    public function test_user_can_delete_pending_document(): void
    {
        $document = Document::create([
            'user_id' => $this->user->id,
            'type' => 'driving_license',
            'filename' => 'test.pdf',
            'path' => 'documents/test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.documents.destroy', $document->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    public function test_user_cannot_delete_approved_document(): void
    {
        $document = Document::create([
            'user_id' => $this->user->id,
            'type' => 'driving_license',
            'filename' => 'test.pdf',
            'path' => 'documents/test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.documents.destroy', $document->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('documents', ['id' => $document->id]);
    }

    public function test_user_cannot_delete_other_users_document(): void
    {
        $otherUser = User::factory()->create();
        $document = Document::create([
            'user_id' => $otherUser->id,
            'type' => 'driving_license',
            'filename' => 'test.pdf',
            'path' => 'documents/test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->delete(route('dashboard.documents.destroy', $document->id));
        $response->assertStatus(403);
    }

    // ───────── Activity Log ─────────

    public function test_document_upload_logs_activity(): void
    {
        $file = UploadedFile::fake()->create('permis.pdf', 200, 'application/pdf');

        $this->actingAs($this->user)->post(route('dashboard.documents.store'), [
            'type' => 'driving_license',
            'file' => $file,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'type' => 'document_uploaded',
        ]);
    }
}
