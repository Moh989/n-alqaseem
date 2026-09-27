<?php

namespace App\Services\Media;

use finfo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Stores the public company profile PDF on the private disk after checking that it is
 * a genuine PDF without active content (JavaScript, launch actions, embedded files).
 */
class PdfUploadService
{
    /**
     * @var list<string>
     */
    protected const FORBIDDEN_TOKENS = ['/JavaScript', '/JS', '/Launch', '/EmbeddedFile', '/RichMedia', '/XFA'];

    /**
     * @return string The stored path on the local (private) disk.
     *
     * @throws ValidationException
     */
    public function store(UploadedFile|string $file, string $locale, string $field = 'pdf'): string
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (($file instanceof UploadedFile && ! $file->isValid()) || ! is_file($path)) {
            $this->fail($field, 'invalid');
        }

        if (filesize($path) > config('site.pdf.max_kilobytes') * 1024) {
            $this->fail($field, 'pdf_too_large');
        }

        if ((new finfo(FILEINFO_MIME_TYPE))->file($path) !== 'application/pdf') {
            $this->fail($field, 'pdf_type');
        }

        $handle = fopen($path, 'rb');
        $header = $handle ? fread($handle, 5) : '';

        if ($handle) {
            fclose($handle);
        }

        if ($header !== '%PDF-') {
            $this->fail($field, 'pdf_type');
        }

        $contents = (string) file_get_contents($path);

        foreach (self::FORBIDDEN_TOKENS as $token) {
            if (preg_match('#'.preg_quote($token, '#').'(?![A-Za-z])#', $contents)) {
                $this->fail($field, 'pdf_active');
            }
        }

        $stored = 'profile/'.$locale.'-'.Str::lower(Str::random(20)).'.pdf';
        Storage::disk('local')->put($stored, $contents);

        return $stored;
    }

    /**
     * @throws ValidationException
     */
    protected function fail(string $field, string $reason): never
    {
        throw ValidationException::withMessages([$field => __('admin.upload.'.$reason)]);
    }
}
