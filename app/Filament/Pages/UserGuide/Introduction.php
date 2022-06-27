<?php

namespace App\Filament\Pages\UserGuide;

use Filament\Pages\Page;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class Introduction extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.user-guide.markdown-parser';

    protected static ?string $navigationGroup = 'User Guide';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'user-guide/introduction';

    protected function getViewData(): array
    {
        return [
            'md' => app(MarkdownRenderer::class)->toHtml(file_get_contents(base_path('docs/Introduction.md'))),
        ];
    }
}
