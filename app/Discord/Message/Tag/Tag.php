<?php

namespace App\Discord\Message\Tag;

use Illuminate\Support\Str;

class Tag implements TagInterface
{
    private readonly TagProviderInterface $tagProvider;

    public function __construct(TagProviderInterface $tagProvider)
    {
        $this->tagProvider = $tagProvider;
    }

    public function __toString(): string
    {
        $tag = $this->tagProvider->rawTagString();

        return sprintf(
            '<%s>',
            Str::startsWith($tag, '@') ? $tag : sprintf('@%s', $tag)
        );
    }
}
