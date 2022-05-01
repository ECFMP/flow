<?php

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('api-spec');
        Storage::disk('api-spec')->put('api-spec-v1.json', json_encode(['foo' => 'bar']));
        Storage::disk('api-spec')->put('api-spec-v123.json', json_encode(['foo' => 'bar']));
    }

    public function testItReturnsNotFoundViewIfSpecDoesntExist()
    {
        $this->get('docs/v2')
            ->assertNotFound();
    }

    public function testItReturnsDocumentationViewIfSpecFound()
    {
        $this->get('docs/v1')
            ->assertOk()
            ->assertSee(url('api/v1'));
    }

    public function testItReturnsDocumentationViewLargeNumberedVersion()
    {
        $this->get('docs/v123')
            ->assertOk()
            ->assertSee(url('api/v123'));
    }

    public function testItReturnsNotFoundViewResponseIfNoVersionNumber()
    {
        $this->get('docs/v')
            ->assertNotFound();
    }

    public function testItReturnsNotFoundViewResponseIfNonNumericVersion()
    {
        $this->get('docs/v2a')
            ->assertNotFound();
    }

    public function testItReturnsNotFoundApiResponseIfSpecDoesntExist()
    {
        $this->get('api/v2')
            ->assertNotFound();
    }

    public function testItReturnsApiSpec()
    {
        $this->get('api/v1')
            ->assertOk()
            ->assertExactJson(['foo' => 'bar']);
    }

    public function testItReturnsApiSpecLargeNumberedVersion()
    {
        $this->get('api/v123')
            ->assertOk()
            ->assertExactJson(['foo' => 'bar']);
    }

    public function testItReturnsNotFoundApiResponseIfNoVersionNumber()
    {
        $this->get('api/v')
            ->assertNotFound();
    }

    public function testItReturnsNotFoundApiResponseIfNonNumericVersion()
    {
        $this->get('api/v2a')
            ->assertNotFound();
    }
}
