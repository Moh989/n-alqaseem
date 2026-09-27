<?php

namespace Tests\Feature;

use App\Models\Setting;
use Database\Seeders\ProfilePdfSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportCompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    protected string $pdfPath;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->seedSite();

        $this->pdfPath = tempnam(sys_get_temp_dir(), 'profile').'.pdf';
        file_put_contents($this->pdfPath, "%PDF-1.6\n1 0 obj << /Type /Catalog >> endobj\n%%EOF");
    }

    protected function tearDown(): void
    {
        @unlink($this->pdfPath);

        parent::tearDown();
    }

    public function test_command_publishes_the_pdf_for_both_languages(): void
    {
        $this->artisan('profile:import', ['path' => $this->pdfPath])->assertSuccessful();

        foreach (['ar', 'en'] as $locale) {
            $setting = Setting::where('key', 'profile_pdf_'.$locale)->sole();
            $this->assertSame(Setting::STATUS_APPROVED, $setting->status);
            Storage::disk('local')->assertExists($setting->value);
        }

        $this->get('/company-profile.pdf')->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->get('/en/company-profile.pdf')->assertOk();
        $this->get('/')->assertSee('href="'.url('/company-profile.pdf').'"', false);
    }

    public function test_reimporting_replaces_the_previous_file(): void
    {
        $this->artisan('profile:import', ['path' => $this->pdfPath])->assertSuccessful();
        $first = Setting::where('key', 'profile_pdf_ar')->value('value');

        $this->artisan('profile:import', ['path' => $this->pdfPath])->assertSuccessful();

        Storage::disk('local')->assertMissing($first);
        Storage::disk('local')->assertExists(Setting::where('key', 'profile_pdf_ar')->value('value'));
    }

    public function test_pending_option_stores_without_publishing(): void
    {
        $this->artisan('profile:import', ['path' => $this->pdfPath, '--pending' => true])->assertSuccessful();

        $this->assertSame(Setting::STATUS_PENDING, Setting::where('key', 'profile_pdf_ar')->value('status'));
        $this->get('/company-profile.pdf')->assertNotFound();
    }

    public function test_invalid_or_missing_files_are_rejected(): void
    {
        file_put_contents($this->pdfPath, 'not a pdf');
        $this->artisan('profile:import', ['path' => $this->pdfPath])->assertFailed();

        $this->artisan('profile:import', ['path' => '/nonexistent/file.pdf'])->assertFailed();

        $this->assertNull(Setting::where('key', 'profile_pdf_ar')->value('value'));
    }

    public function test_seeder_is_skipped_during_tests_and_never_overwrites_existing_files(): void
    {
        Setting::where('key', 'profile_pdf_ar')->update(['value' => 'profile/existing.pdf']);

        $this->seed(ProfilePdfSeeder::class);

        $this->assertSame('profile/existing.pdf', Setting::where('key', 'profile_pdf_ar')->value('value'));
    }
}
