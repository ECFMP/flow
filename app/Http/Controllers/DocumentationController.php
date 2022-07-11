<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentationController
{
    public function getDocumentationData(int $version): Response
    {
        $this->checkVersionExists($version);

        return response(Storage::disk('api-spec')->get($this->specFilename($version)))
            ->withHeaders(['Content-Type' => 'application/json']);
    }

    public function documentationView(int $version): View
    {
        $this->checkVersionExists($version);

        return view('api-docs', ['version' => $version]);
    }

    private function checkVersionExists(int $version): void
    {
        if (!Storage::disk('api-spec')->exists($this->specFilename($version))) {
            abort(404);
        }
    }

    private function specFilename(int $version): string
    {
        return sprintf('api-spec-v%d.json', $version);
    }
}
