<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Description\EventNameAndInterestedParties;
use App\Discord\FlowMeasure\Field\ArrivalAirports;
use App\Discord\FlowMeasure\Field\DepartureAirports;
use App\Discord\FlowMeasure\Field\EndTime;
use App\Discord\FlowMeasure\Field\Filters\AdditionalFilterParser;
use App\Discord\FlowMeasure\Field\Reason;
use App\Discord\FlowMeasure\Field\Restriction;
use App\Discord\FlowMeasure\Field\StartTime;
use App\Discord\FlowMeasure\Title\IdentifierAndStatus;
use App\Discord\Message\Embed\BlankField;
use App\Discord\Message\Embed\Colour;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Embed\Field;
use App\Discord\Message\Embed\FieldProviderInterface;
use App\Discord\Message\MessageInterface;
use App\Models\FlowMeasure;

class FlowMeasureNotifiedMessage implements MessageInterface
{
    private readonly FlowMeasure $measure;

    public function __construct(FlowMeasure $measure)
    {
        $this->measure = $measure;
    }

    public function content(): string
    {
        return '';
    }

    public function embeds(): EmbedCollection
    {
        return (new FlowMeasureActivatedMessage($this->measure))->embeds();
    }
}
