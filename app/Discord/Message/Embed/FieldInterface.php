<?php

namespace App\Discord\Message\Embed;

interface FieldInterface
{
    /**
     * The name of the field.
     */
    public function name(): string;

    /**
     * The fields value.
     */
    public function value(): string;

    /**
     * Should the field be inline
     */
    public function inline(): bool;
}
