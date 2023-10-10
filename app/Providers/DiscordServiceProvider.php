<?php

namespace App\Providers;

use App\Discord\Client\ClientFactory;
use App\Discord\DiscordInterface;
use App\Discord\DiscordMessageSender;
use App\Discord\DiscordServiceMessageSender;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Message\MessageGenerator;
use App\Discord\FlowMeasure\Message\MessageGeneratorInterface;
use App\Discord\FlowMeasure\Provider\MessageProvider;
use App\Discord\FlowMeasure\Webhook\Filter\ActivatedWebhookFilter;
use App\Discord\FlowMeasure\Webhook\Filter\ExpiredWebhookFilter;
use App\Discord\FlowMeasure\Webhook\Filter\FilterInterface;
use App\Discord\FlowMeasure\Webhook\Filter\NotifiedWebhookFilter;
use App\Discord\FlowMeasure\Webhook\Filter\WithdrawnWebhookFilter;
use App\Discord\FlowMeasure\Webhook\WebhookMapper;
use App\Discord\Message\Sender\Sender;
use App\Discord\Webhook\EcfmpWebhook;
use App\Repository\FlowMeasureNotification\ActiveRepository;
use App\Repository\FlowMeasureNotification\ExpiredRepository;
use App\Repository\FlowMeasureNotification\NotifiedRepository;
use App\Repository\FlowMeasureNotification\RepositoryInterface;
use App\Repository\FlowMeasureNotification\WithdrawnRepository;
use Ecfmp_discord\DiscordClient;
use Grpc\ChannelCredentials;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    private const FLOW_MEASURE_MESSAGE_REPOSITORIES = [
        NotifiedRepository::class => NotifiedWebhookFilter::class,
        ActiveRepository::class => ActivatedWebhookFilter::class,
        WithdrawnRepository::class => WithdrawnWebhookFilter::class,
        ExpiredRepository::class => ExpiredWebhookFilter::class,
    ];

    public function register(): void
    {
        $this->app->singleton(DiscordInterface::class, function () {
            return new DiscordMessageSender();
        });
        $this->app->singleton(EcfmpWebhook::class);
        $this->app->singleton(
            Sender::class,
            fn() => new Sender(
                $this->flowMeasureMessageProviders(),
                $this->app->make(DiscordInterface::class),
                $this->app->make(DiscordServiceMessageSender::class)
            )
        );

        $this->app->singleton(ClientFactory::class);
    }

    private function flowMeasureMessageProviders(): array
    {
        $providers = [];

        foreach (self::FLOW_MEASURE_MESSAGE_REPOSITORIES as $repository => $filter) {
            $providers[] = $this->makeMessageProvider(
                $this->app->make($repository),
                $this->app->make($filter)
            );
        }

        return $providers;
    }

    private function makeMessageProvider(
        RepositoryInterface $repository,
        FilterInterface $filter
    ): MessageGeneratorInterface {
        return new MessageGenerator(
            new MessageProvider(
                $repository,
                $this->app->make(
                    WebhookMapper::class,
                    [
                        'filter' => $filter,
                    ]
                )
            ),
            $this->app->make(FlowMeasureMessageFactory::class)
        );
    }
}
