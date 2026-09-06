<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\BannerFactory,
    Seeds\Records\BannerRecord
};

use App\Core\Domain\Segment\Banner\Enum\BannerType;

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class BannerFixture extends AbstractFixture implements FixtureGroupInterface
{
    use BannerFactory;

    /**
     * @param BannerRecord $bannerRecord
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly BannerRecord $bannerRecord,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @return iterable<mixed>
    */
    protected function getData(): iterable
    {
        return $this->bannerRecord->fetchData();
    }

    /**
     * @param mixed $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        /**
         * @var array{
         *     position: int,
         *     name: string,
         *     image: string|null,
         *     url: string|null,
         *     type: string,
         *     is_active: bool
         * } $data
        */
        $type = BannerType::from($data['type']);

        $banner = $this->createBanner(
            position: (int) $data['position'],
            name: $data['name'],
            image: $data['image'],
            url: $data['url'],
            type: $type,
            isActive: (bool) $data['is_active'],
        );

        $manager->persist($banner);
    }
}
