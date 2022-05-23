<?php

namespace App\Discord\Message\Embed;

interface FooterInterface
{
    /**
     * Get the footer text
     *
     * @return string
     */
    public function footer(): string;
}
