<?php

namespace App\Filament\Pages\UserGuide;

use Filament\Pages\Page;
use Illuminate\View\View;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class FlowMeasuresBasics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.user-guide.markdown-parser';

    protected static ?string $navigationGroup = 'User Guide';

    protected static ?string $navigationLabel = 'FM: Basics';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'user-guide/fm-basics';

    protected function getHeader(): View
    {
        return view('empty');
    }

    protected function getViewData(): array
    {
        return [
            'md' => app(MarkdownRenderer::class)->toHtml(file_get_contents(base_path('docs/FM Basics.md'))),
        ];
    }
}
