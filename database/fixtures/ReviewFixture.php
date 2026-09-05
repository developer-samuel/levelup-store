<?php

declare(strict_types=1);

namespace Database\Fixtures;

use Doctrine\{
    Common\DataFixtures\DependentFixtureInterface,
    Persistence\ObjectManager
};

use Database\{
    Seeds\Abstract\AbstractFixture,
    Seeds\Builders\ReviewBuilder
};

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\User\Repository\UserRepositoryContract,
    Shared\Logging\AppLoggerContract,
    Shared\Logging\ConsoleLoggerContract
};

final class ReviewFixture extends AbstractFixture implements DependentFixtureInterface
{
    use ReviewBuilder;

    /**
     * @param ProductVariantRepositoryContract $variantRepository
     * @param UserRepositoryContract $userRepository
     * @param AppLoggerContract $appLogger
     * @param ConsoleLoggerContract $consoleLogger
    */
    public function __construct(
        private readonly ProductVariantRepositoryContract $variantRepository,
        private readonly UserRepositoryContract $userRepository,
        AppLoggerContract $appLogger,
        ConsoleLoggerContract $consoleLogger,
    ) {
        parent::__construct(
            $appLogger,
            $consoleLogger,
        );
    }

    /**
     * @return string[]
    */
    public function getDependencies(): array
    {
        return [
            ProductFixture::class,
            UserFixture::class,
        ];
    }

    /**
     * @return iterable<int, array{variant: object, user: object}>
    */
    protected function getData(): iterable
    {
        $variants = $this->variantRepository->findAll();
        $users = $this->userRepository->findAll();

        if (empty($variants)) {
            $this->consoleLogger->logError('ReviewFixture: No product variants found. Make sure ProductFixture ran first.');

            return [];
        }

        if (empty($users)) {
            $this->consoleLogger->logError('ReviewFixture: No users found. Make sure UserFixture ran first.');

            return [];
        }

        foreach ($variants as $variant) {
            yield from $this->yieldRandomUsersForVariant($variant, $users);
        }
    }

    /**
     * @param array{variant: ProductVariant, user: User} $data
     * @param ObjectManager $manager
     *
     * @return void
    */
    protected function createEntity(mixed $data, ObjectManager $manager): void
    {
        $userId = IdAssertion::assert(
            $data['user']->getId(),
            'User ID',
        );

        /** @var User $user */
        $user = $this->userRepository->findById($userId);

        $variants = $this->resolveVariants($data['variant']);

        $reviews = $this->createReviewsWithRatings(
            $manager,
            $variants,
            $user,
        );

        foreach ($reviews as $review) {
            $manager->persist($review);
        }
    }

    /**
     * @param ProductVariant $variant
     * @param User[] $users
     *
     * @return iterable<int, array{variant: ProductVariant, user: User}>
    */
    private function yieldRandomUsersForVariant(ProductVariant $variant, array $users): iterable
    {
        $numUsers = rand(1, min(5, count($users)));
        $selectedKeys = (array) array_rand($users, $numUsers);

        foreach ($selectedKeys as $key) {
            $user = $users[$key];

            if ($user->getRole() !== UserRole::USER) {
                continue;
            }

            yield [
                'variant' => $variant,
                'user'    => $user,
            ];
        }
    }

    /**
     * @param ProductVariant|ProductVariant[] $variant
     *
     * @return ProductVariant[]
    */
    private function resolveVariants(ProductVariant|array $variant): array
    {
        $variants = $this->normalizeVariants($variant);

        return $this->fetchVariantsFromRepository($variants);
    }

    /**
     * @param ProductVariant|ProductVariant[] $variant
     *
     * @return ProductVariant[]
    */
    private function normalizeVariants(ProductVariant|array $variant): array
    {
        return is_array($variant) ? $variant : [$variant];
    }

    /**
     * @param ProductVariant[] $variants
     *
     * @return ProductVariant[]
    */
    private function fetchVariantsFromRepository(array $variants): array
    {
        $resolved = array_map(
            $this->resolveSingleVariant(...),
            $variants,
        );

        return $this->filterValidVariants($resolved);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariant|null
    */
    private function resolveSingleVariant(ProductVariant $variant): ?ProductVariant
    {
        return $this->variantRepository->findById(
            IdAssertion::assert($variant->getId(), 'Variant ID'),
        );
    }

    /**
     * @param mixed[] $items
     *
     * @return ProductVariant[]
    */
    private function filterValidVariants(array $items): array
    {
        return array_values(array_filter(
            $items,
            static fn($v): bool => $v instanceof ProductVariant,
        ));
    }
}
