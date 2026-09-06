<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Common\DataFixtures\DependentFixtureInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Factories\Product\Variant\VariantRecommendedFactory,
    Seeds\Utils\Generator\Product\VariantRecommendedGenerator
};

use Kit\{
    Assertion\Domain\Product\Variant\ProductVariantAssertion,
    Assertion\Shared\IdAssertion
};

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class VariantRecommendedFixture extends AbstractFixture implements DependentFixtureInterface
{
    use VariantRecommendedFactory;

    /**
     * @param VariantRecommendedGenerator $variantRecommendedGenerator
     * @param ProductVariantRepositoryContract $variantRepository
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly VariantRecommendedGenerator $variantRecommendedGenerator,
        private readonly ProductVariantRepositoryContract $variantRepository,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @return class-string[]
    */
    public function getDependencies(): array
    {
        return [
            ProductFixture::class,
        ];
    }

    /**
     * @return iterable<array<string, mixed>>
    */
    protected function getData(): iterable
    {
        $variants = $this->variantRepository->findAll();

        return $this->variantRecommendedGenerator->fetchData($variants);
    }

    /**
     * @param array{
     *     variant: ProductVariant,
     *     position: int
     * } $data
     * @param ObjectManager $manager
     *
     * @return void
     *
     * @throws \LogicException
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $variantId = IdAssertion::assert(
            $data['variant']->getId(),
            'Variant ID',
            \LogicException::class,
        );

        $variant = $this->variantRepository->findById($variantId);
        ProductVariantAssertion::assertExists($variant);

        $recommended = $this->createVariant(
            $variant,
            $data['position'],
        );

        $manager->persist($recommended);
    }
}
