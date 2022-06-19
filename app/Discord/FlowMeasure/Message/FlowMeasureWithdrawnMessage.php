<?php

namespace App\Discord\FlowMeasure\Message;

use App\Discord\FlowMeasure\Content\InterestedParties;
use App\Discord\FlowMeasure\Description\EventName;
use App\Discord\FlowMeasure\Field\ArrivalAirports;
use App\Discord\FlowMeasure\Field\DepartureAirports;
use App\Discord\FlowMeasure\Field\Restriction;
use App\Discord\FlowMeasure\Title\IdentifierAndWithdrawnStatus;
use App\Discord\Message\Embed\Colour;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Embed\Field;
use App\Discord\Message\MessageInterface;
use App\Models\FlowMeasure;

class FlowMeasureWithdrawnMessage implements MessageInterface
{
    private readonly FlowMeasure $measure;

    public function __construct(FlowMeasure $measure)
    {
        $this->measure = $measure;
    }

    public function content(): string
    {
        return InterestedParties::interestedPartiesString($this->measure);
    }

    public function embeds(): EmbedCollection
    {
        return (new EmbedCollection())->add(
            Embed::make()->withColour(Colour::WITHDRAWN)
                ->withTitle(new IdentifierAndWithdrawnStatus($this->measure))
                ->withDescription(new EventName($this->measure))
                ->withField(Field::makeInline(new Restriction($this->measure)))
                ->withField(Field::makeInline(new DepartureAirports($this->measure)))
                ->withField(Field::makeInline(new ArrivalAirports($this->measure)))
        );
    }
}
