<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
        $this->seedSite();
        $this->actingAs(User::factory()->create());
    }

    /**
     * Submit the "new slide" form with the given image.
     */
    protected function uploadSlide(UploadedFile $image): TestResponse
    {
        return $this->post(route('admin.slides.store'), [
            'page' => 'home',
            'heading_ar' => 'شريحة اختبار',
            'is_published' => '0',
            'image' => $image,
        ]);
    }

    public function test_a_valid_jpeg_is_reencoded_into_webp_renditions_without_metadata(): void
    {
        $file = UploadedFile::fake()->image('site photo.jpg', 1600, 1000);

        $this->uploadSlide($file)
            ->assertSessionHasNoErrors();

        $media = Media::sole();
        $this->assertSame([480, 768, 1280], $media->widths);
        $this->assertMatchesRegularExpression('#^media/\d{4}/\d{2}/[a-z0-9]{24}$#', $media->directory);

        foreach (['w480.webp', 'w768.webp', 'w1280.webp', 'fallback.jpg', 'og.jpg'] as $rendition) {
            Storage::disk('public')->assertExists($media->directory.'/'.$rendition);
        }

        // The original upload is never stored.
        $this->assertCount(5, Storage::disk('public')->files($media->directory));
    }

    public function test_svg_uploads_are_rejected(): void
    {
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $this->uploadSlide($svg)
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_php_disguised_as_an_image_is_rejected(): void
    {
        $php = UploadedFile::fake()->createWithContent('shell.jpg', '<?php system($_GET["c"]); ?>');

        $this->uploadSlide($php)
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_gif_polyglot_is_rejected(): void
    {
        $gif = UploadedFile::fake()->createWithContent('image.png', "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;<?php echo 1; ?>");

        $this->uploadSlide($gif)
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_images_that_are_too_small_are_rejected(): void
    {
        $this->uploadSlide(UploadedFile::fake()->image('tiny.png', 300, 200))
            ->assertSessionHasErrors('image');
    }

    public function test_a_valid_pdf_is_stored_privately_and_awaits_approval(): void
    {
        $pdf = UploadedFile::fake()->createWithContent('profile.pdf', "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\n%%EOF");

        $this->post(route('admin.profile-pdf.store', 'ar'), ['pdf' => $pdf])->assertSessionHasNoErrors();

        $setting = Setting::where('key', 'profile_pdf_ar')->sole();
        $this->assertStringStartsWith('profile/ar-', $setting->value);
        $this->assertSame(Setting::STATUS_PENDING, $setting->status);
        Storage::disk('local')->assertExists($setting->value);
        Storage::disk('public')->assertMissing($setting->value);
    }

    public function test_fake_pdf_and_pdf_with_javascript_are_rejected(): void
    {
        $fake = UploadedFile::fake()->createWithContent('profile.pdf', 'not a pdf at all');
        $this->post(route('admin.profile-pdf.store', 'ar'), ['pdf' => $fake])->assertSessionHasErrors('pdf');

        $active = UploadedFile::fake()->createWithContent('profile.pdf', "%PDF-1.4\n1 0 obj << /OpenAction << /S /JavaScript /JS (app.alert(1)) >> >> endobj\n%%EOF");
        $this->post(route('admin.profile-pdf.store', 'ar'), ['pdf' => $active])->assertSessionHasErrors('pdf');

        $this->assertNull(Setting::where('key', 'profile_pdf_ar')->value('value'));
    }
}
