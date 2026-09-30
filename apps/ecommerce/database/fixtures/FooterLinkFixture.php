<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Bundle\FixturesBundle\FixtureGroupInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\FooterLinkFactory,
    Seeds\Records\FooterLinkRecord
};

use App\Core\Domain\{
    Segment\Footer\Enum\FooterLinkGroup,
    Segment\Footer\Enum\FooterLinkTarget
};

use App\Core\Ports\{
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class FooterLinkFixture extends AbstractFixture implements FixtureGroupInterface
{
    use FooterLinkFactory;

    /**
     * @param FooterLinkRecord $footerLinkRecord
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly FooterLinkRecord $footerLinkRecord,
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
        return $this->footerLinkRecord->fetchData();
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
         *     value: string,
         *     image: string|null,
         *     url: string,
         *     group: string,
         *     target: string
         * } $data
        */
        $group = FooterLinkGroup::from($data['group']);
        $target = FooterLinkTarget::from($data['target']);

        $footerLink = $this->createFooterLink(
            position: (int) $data['position'],
            value: $data['value'],
            image: $data['image'],
            url: $data['url'],
            group: $group,
            target: $target,
        );

        $manager->persist($footerLink);
    }
}
