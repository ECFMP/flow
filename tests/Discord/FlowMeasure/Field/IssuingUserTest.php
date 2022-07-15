<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\IssuingUser;
use App\Models\FlowMeasure;
use Tests\TestCase;

class IssuingUserTest extends TestCase
{
    private readonly FlowMeasure $flowMeasure;
    private readonly IssuingUser $issuingUser;

    public function setUp(): void
    {
        parent::setUp();
        $this->flowMeasure = FlowMeasure::factory()->make();
        $this->issuingUser = new IssuingUser($this->flowMeasure);
    }

    public function testItHasAName()
    {
        $this->assertEquals('Issued By', $this->issuingUser->name());
    }

    public function testItHasAValue()
    {
        $this->assertEquals($this->flowMeasure->user->name, $this->issuingUser->value());
    }
}
