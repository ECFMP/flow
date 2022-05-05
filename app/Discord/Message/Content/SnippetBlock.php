<?php

namespace App\Discord\Message\Content;

class SnippetBlock implements ContentInterface
{
    private readonly ContentInterface $childContent;

    public function __construct(ContentInterface $childContent)
    {
        $this->childContent = $childContent;
    }

    public function toString(): string
    {
        return sprintf("```\n%s\n```", $this->childContent->toString());
    }
}
