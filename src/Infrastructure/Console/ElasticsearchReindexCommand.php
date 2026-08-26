<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use Symfony\{
    Component\Console\Attribute\AsCommand,
    Component\Console\Command\Command,
    Component\Console\Input\InputInterface,
    Component\Console\Output\OutputInterface,
    Component\DependencyInjection\Attribute\AutowireIterator
};

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Shared\Logging\ConsoleLoggerContract,
    Shared\ReindexableInterface
};

#[AsCommand(
    name: 'app:elasticsearch:reindex',
    description: 'Reindexes all Elasticsearch indexes.',
)]
final class ElasticsearchReindexCommand extends Command
{
    /**
     * @param iterable<ReindexableInterface> $reindexables
    */
    public function __construct(
        private readonly ElasticsearchGatewayContract $elasticsearch,
        private readonly ConsoleLoggerContract $logger,
        #[AutowireIterator('app.elasticsearch.reindexable')]
        private readonly iterable $reindexables,
    ) {
        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
    */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->elasticsearch->isEnabled()) {
            $this->logger->logWarning('Elasticsearch is disabled (ELASTICSEARCH_ENABLED=false). Reindex skipped.');

            return Command::SUCCESS;
        }

        foreach ($this->reindexables as $reindexable) {
            $this->logger->logMessage(sprintf('Reindexing: %s', $reindexable->getIndexName()));

            $indexed = $reindexable->reindexAll();

            $this->logger->logSuccess(sprintf('Indexed %d records into "%s".', $indexed, $reindexable->getIndexName()));
        }

        return Command::SUCCESS;
    }
}
