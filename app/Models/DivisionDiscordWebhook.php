<?php

namespace App\Models;

use App\Discord\Message\Tag\TagProviderInterface;
use App\Discord\Webhook\WebhookInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionDiscordWebhook extends Model implements WebhookInterface, TagProviderInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'url',
        'description',
        'tag',
    ];

    public function flightInformationRegions(): BelongsToMany
    {
        return $this->belongsToMany(FlightInformationRegion::class);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function rawTagString(): string
    {
        return $this->tag;
    }
}
