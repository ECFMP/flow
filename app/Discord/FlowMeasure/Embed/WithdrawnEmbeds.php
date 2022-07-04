<?php

namespace App\Discord\FlowMeasure\Embed;

use App\Discord\FlowMeasure\Description\EventName;
use App\Discord\FlowMeasure\Field\ArrivalAirports;
use App\Discord\FlowMeasure\Field\DepartureAirports;
use App\Discord\FlowMeasure\Field\Restriction;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\FlowMeasure\Title\IdentifierAndWithdrawnStatus;
use App\Discord\Message\Embed\Colour;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Embed\Field;

class WithdrawnEmbeds implements FlowMeasureEmbedInterface
{
    private readonly PendingMessageInterface $pendingMessage;

    public function __construct(PendingMessageInterface $pendingMessage)
    {
        $this->pendingMessage = $pendingMessage;
    }

    public function embeds(): EmbedCollection
    {
        return EmbedCollection::make()->add(
            Embed::make()->withColour(Colour::WITHDRAWN)
                ->withTitle(new IdentifierAndWithdrawnStatus($this->pendingMessage->flowMeasure()))
                ->withDescription(new EventName($this->pendingMessage->flowMeasure()))
                ->withField(Field::makeInline(new Restriction($this->pendingMessage->flowMeasure())))
                ->withField(Field::makeInline(new DepartureAirports($this->pendingMessage->flowMeasure())))
                ->withField(Field::makeInline(new ArrivalAirports($this->pendingMessage->flowMeasure())))
        );
    }
}
