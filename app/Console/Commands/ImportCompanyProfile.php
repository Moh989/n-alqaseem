<?php

namespace App\Console\Commands;

use App\Services\Media\CompanyProfileImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

#[Signature('profile:import {path? : PDF file to publish (default: Web_profile.pdf in the project folder)} {--pending : Store the file without approving it for publication}')]
#[Description('Publish the company profile PDF shown by the "Company profile" buttons (Arabic and English)')]
class ImportCompanyProfile extends Command
{
    public function handle(CompanyProfileImporter $importer): int
    {
        $path = $this->argument('path') ?? CompanyProfileImporter::defaultPath();

        if ($path === null || ! is_file($path)) {
            $this->components->error('PDF not found. Place Web_profile.pdf in the project folder or pass its path.');

            return self::FAILURE;
        }

        try {
            $importer->import($path, approve: ! $this->option('pending'));
        } catch (ValidationException $exception) {
            $this->components->error(collect($exception->errors())->flatten()->implode(' '));

            return self::FAILURE;
        }

        $this->components->info($this->option('pending')
            ? 'Profile PDF stored and awaiting approval in the admin panel.'
            : 'Profile PDF published: '.lroute('profile.download', [], 'ar', false));

        return self::SUCCESS;
    }
}
