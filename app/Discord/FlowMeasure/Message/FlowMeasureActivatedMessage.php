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
use App\Discord\FlowMeasure\Footer\IntendedRecipients;
use App\Discord\FlowMeasure\Title\Identifier;
use App\Discord\Message\Embed\BlankField;
use App\Discord\Message\Embed\Colour;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Embed\Field;
use App\Discord\Message\Embed\FieldProviderInterface;
use App\Discord\Message\MessageInterface;
use App\Models\FlowMeasure;

class FlowMeasureActivatedMessage implements MessageInterface
{
    private readonly FlowMeasure $measure;

    public function __construct(FlowMeasure $measure)
    {
        $this->measure = $measure;
    }

    public function content(): string
    {
        return 'Flow Measure Activated';
    }

    public function embeds(): EmbedCollection
    {
        return (new EmbedCollection())->add(
            Embed::make()->withColour(Colour::ACTIVATED)
                ->withTitle(new Identifier($this->measure))
                ->withDescription(new EventNameAndInterestedParties($this->measure))
                ->withField(Field::makeInline(new Restriction($this->measure)))
                ->withField(Field::makeInline(new StartTime($this->measure)))
                ->withField(Field::makeInline(new EndTime($this->measure)))
                ->withField(Field::makeInline(new DepartureAirports($this->measure)))
                ->withField(Field::makeInline(new ArrivalAirports($this->measure)))
                ->withField(Field::makeInline(BlankField::make()))
                ->withFields(
                    AdditionalFilterParser::parseAdditionalFilters($this->measure)->map(
                        fn(FieldProviderInterface $provider) => Field::make($provider)
                    )
                )
                ->withField(
                    Field::make(new Reason($this->measure))
                )
        );
    }
}
