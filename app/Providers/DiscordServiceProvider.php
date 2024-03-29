<?php

namespace App\Providers;

use App\Discord\Client\ClientFactory;
use App\Discord\Client\ClientFactoryInterface;
use App\Discord\DiscordServiceInterface;
use App\Discord\DiscordServiceMessageSender;
use App\Discord\DiscordWebhookInterface;
use App\Discord\DiscordWebhookSender;
use App\Discord\FlowMeasure\Generator\EcfmpFlowMeasureMessageGenerator;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Message\MessageGenerator;
use App\Discord\FlowMeasure\Message\MessageGeneratorInterface;
use App\Discord\FlowMeasure\Provider\DivisionWebhookMessageProvider;
use App\Discord\FlowMeasure\Sender\EcfmpFlowMeasureSender;
use App\Discord\FlowMeasure\Webhook\Filter\ActivatedWebhookFilter;
use App\Discord\FlowMeasure\Webhook\Filter\ExpiredWebhookFilter;
use App\Discord\FlowMeasure\Webhook\Filter\FilterInterface;
use App\Discord\FlowMeasure\Webhook\Filter\NotifiedWebhookFilter;
use App\Discord\FlowMeasure\Webhook\Filter\WithdrawnWebhookFilter;
use App\Discord\FlowMeasure\Webhook\WebhookMapper;
use App\Discord\Message\Sender\DivisionWebhookSender;
use App\Repository\FlowMeasureNotification\ActiveRepository;
use App\Repository\FlowMeasureNotification\ExpiredRepository;
use App\Repository\FlowMeasureNotification\NotifiedRepository;
use App\Repository\FlowMeasureNotification\RepositoryInterface;
use App\Repository\FlowMeasureNotification\WithdrawnRepository;
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
        $this->app->singleton(DiscordWebhookInterface::class, function () {
            return new DiscordWebhookSender();
        });
        $this->app->singleton(
            DivisionWebhookSender::class,
            fn () => new DivisionWebhookSender(
                $this->flowMeasureMessageProviders(),
                $this->app->make(DiscordWebhookInterface::class)
            )
        );

        $this->app->singleton(
            DiscordServiceInterface::class,
            fn () => $this->app->make(DiscordServiceMessageSender::class)
        );
        $this->app->singleton(ClientFactoryInterface::class, ClientFactory::class);
        $this->app->singleton(ClientFactory::class);
        $this->app->singleton(
            EcfmpFlowMeasureMessageGenerator::class,
            function () {
                return new EcfmpFlowMeasureMessageGenerator(
                    $this->app->make(EcfmpFlowMeasureSender::class),
                    array_map(
                        fn (string $repository) => $this->app->make($repository),
                        array_keys(self::FLOW_MEASURE_MESSAGE_REPOSITORIES)
                    )
                );
            }
        );
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
            new DivisionWebhookMessageProvider(
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
