<?php

namespace App\Discord\Message\Embed;

interface FieldProviderInterface
{
    /**
     * The name of the field.
     */
    public function name(): string;

    /**
     * The fields value.
     */
    public function value(): string;
}
