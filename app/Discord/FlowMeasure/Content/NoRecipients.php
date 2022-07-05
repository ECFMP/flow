<?php

namespace App\Discord\FlowMeasure\Content;

class NoRecipients implements FlowMeasureRecipientsInterface
{
    public function toString(): string
    {
        return '';
    }
}
