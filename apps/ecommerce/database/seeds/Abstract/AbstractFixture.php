<?php

declare(strict_types=1);

namespace Database\Seeds\Abstract;

use Doctrine\{
    Bundle\FixturesBundle\Fixture,
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

abstract class AbstractFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        protected readonly AppLoggerContract $appLogger,
        protected readonly ConsoleLoggerContract $consoleLogger,
    ) {}

    /** @return iterable<mixed> */
    abstract protected function getData(): iterable;

    abstract protected function createEntity(mixed $data, ObjectManager $manager): void;

    /** @return string[] */
    final public static function getGroups(): array
    {
        return ['small_batch'];
    }

    public function load(ObjectManager $manager): void
    {
        try {
            $this->processData($manager);

            $this->consoleLogger->logSuccess(static::class . " loaded successfully!");
        } catch (\Throwable $throwable) {
            $this->handleException($throwable);
        }
    }

    private function processData(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $this->createEntity($data, $manager);

            $manager->flush();
            $manager->clear();
        }
    }

    private function handleException(\Throwable $throwable): void
    {
        $message = 'Error flushing ' . static::class . ': ' . $throwable->getMessage();

        $this->appLogger->logThrowable(
            $message,
            $throwable,
            null,
            ['seeder' => static::class],
        );

        $this->consoleLogger->logError($message);
    }
}
