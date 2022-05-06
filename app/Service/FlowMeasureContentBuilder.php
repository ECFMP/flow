<?php

namespace App\Service;

use App\Discord\FlowMeasure\Content\Arriving;
use App\Discord\FlowMeasure\Content\Departing;
use App\Discord\FlowMeasure\Content\Identifier;
use App\Discord\FlowMeasure\Content\IntendedRecipients;
use App\Discord\FlowMeasure\Content\Measure;
use App\Discord\FlowMeasure\Content\OtherFilters;
use App\Discord\FlowMeasure\Content\Reason;
use App\Discord\FlowMeasure\Content\ValidPeriod;
use App\Discord\Message\Content\Composite;
use App\Discord\Message\Content\ContentInterface;
use App\Discord\Message\Content\Newline;
use App\Discord\Message\Content\SnippetBlock;
use App\Discord\Message\Content\Spacing;
use App\Models\FlowMeasure;

class FlowMeasureContentBuilder
{
    public static function activated(FlowMeasure $flowMeasure): ContentInterface
    {
        return Composite::make()
            ->addComponent(new SnippetBlock(self::getInformationSnippet($flowMeasure)))
            ->addComponent(Newline::make(2))
            ->addComponent(new IntendedRecipients($flowMeasure));
    }

    private static function getInformationSnippet(FlowMeasure $flowMeasure): ContentInterface
    {
        return Composite::make()
            ->addComponent(new Identifier($flowMeasure))
            ->addComponent(Newline::make(2))
            ->addComponent(new Measure($flowMeasure))
            ->addComponent(Newline::make())
            ->addComponent(new Departing($flowMeasure))
            ->addComponent(Spacing::make(10))
            ->addComponent(new Arriving($flowMeasure))
            ->addComponent(Newline::make())
            ->addComponent(new OtherFilters($flowMeasure))
            ->addComponent(Newline::make())
            ->addComponent(new ValidPeriod($flowMeasure))
            ->addComponent(Newline::make(2))
            ->addComponent(new Reason($flowMeasure));
    }
}
