<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileDownloadController extends Controller
{
    /**
     * Stream the approved company profile PDF (the other language's file is used when one is missing).
     */
    public function __invoke(): BinaryFileResponse
    {
        $path = site()->profilePdfPath();

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $filename = 'Noor-AlQaseem-Company-Profile.pdf';

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
