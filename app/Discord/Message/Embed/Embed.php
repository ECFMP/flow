<?php

namespace App\Discord\Message\Embed;

use Illuminate\Support\Collection;

class Embed implements EmbedInterface
{
    private AuthorInterface $author;
    private DescriptionInterface $description;
    private readonly Collection $fields;
    private FooterInterface $footer;
    private TitleInterface $title;
    private Colour $colour;

    private function __construct()
    {
        $this->fields = collect();
    }

    public static function make(): static
    {
        return new static();
    }

    public function withAuthor(AuthorInterface $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function withColour(Colour $colour): static
    {
        $this->colour = $colour;

        return $this;
    }

    public function withTitle(TitleInterface $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function withDescription(DescriptionInterface $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function withField(FieldInterface $field): static
    {
        $this->fields->add($field);

        return $this;
    }

    public function withFields(Collection $fields): static
    {
        $fields->each(function (FieldInterface $field) {
            $this->fields->add($field);
        });

        return $this;
    }

    public function withFooter(FooterInterface $footer): static
    {
        $this->footer = $footer;

        return $this;
    }

    public function toArray(): array
    {
        $return = [];

        if (isset($this->title)) {
            $return['title'] = $this->title->title();
        }

        if (isset($this->colour)) {
            $return['color'] = $this->colour->value;
        }

        if (isset($this->author)) {
            $return['author'] = $this->author->author();
        }

        if (isset($this->description)) {
            $return['description'] = $this->description->description();
        }

        if ($this->fields->isNotEmpty()) {
            $return['fields'] = $this->fields->map(fn (FieldInterface $field) => [
                'name' => $field->name(),
                'value' => $field->value(),
                'inline' => $field->inline(),
            ])->toArray();
        }

        if (isset($this->footer)) {
            $return['footer'] = [
                'text' => $this->footer->footer(),
            ];
        }

        return $return;
    }
}
