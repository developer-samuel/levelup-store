Write PHPUnit tests for a given ecommerce class.

The user will provide a class path or name. Generate a complete test file.

## Steps

1. Read the source class fully
2. Identify the test type based on the class layer:
   - `Application/` service or handler → **Unit test**
   - `Adapters/`, `Infrastructure/` concrete class → **Integration test**
   - `Presentation/Api/` or `Presentation/Web/` controller → **Feature test**
3. Read existing tests in the same layer for conventions
4. Generate the test file

## Test path mapping

| Source                                | Test                                                           |
|---------------------------------------|----------------------------------------------------------------|
| `src/Core/Application/{path}/Foo.php` | `tests/php-unit/Unit/Application/{path}/FooTest.php`           |
| `src/Infrastructure/{path}/Foo.php`   | `tests/php-unit/Integration/Infrastructure/{path}/FooTest.php` |
| `src/Adapters/{path}/Foo.php`         | `tests/php-unit/Integration/Adapters/{path}/FooTest.php`       |
| `src/Presentation/Api/{path}/Foo.php` | `tests/php-unit/Feature/Presentation/Api/{path}/FooTest.php`   |
| `src/Presentation/Web/{path}/Foo.php` | `tests/php-unit/Feature/Presentation/Web/{path}/FooTest.php`   |

## Unit test template

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{FullPath};

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Ports\{Segment}\{DependencyContract};
use App\Core\{FullPath}\{ClassName};

/** @coversDefaultClass \App\Core\{FullPath}\{ClassName} */
final class {ClassName}Test extends TestCase
{
    private {DependencyContract}&MockObject $dependency;
    private {ClassName} $sut;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initSut();
    }

    public function test{Behavior}(): void
    {
        // Arrange
        // Act
        // Assert
    }

    private function initMocks(): void
    {
        $this->dependency = $this->createMock({DependencyContract}::class);
    }

    private function initSut(): void
    {
        $this->sut = new {ClassName}($this->dependency);
    }
}
```

## Integration test template

```php
<?php

declare(strict_types=1);

namespace Tests\Integration\{FullPath};

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\{FullPath}\{ClassName};
use Tests\Support\Provides\Persistence;

/** @coversDefaultClass \App\{FullPath}\{ClassName} */
final class {ClassName}Test extends KernelTestCase
{
    use Persistence;

    private EntityManagerInterface $em;
    private {ClassName} $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = $this->getEntityManager();
        $this->repository = $this->getRepository();

        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function test{Behavior}(): void
    {
        // Arrange
        // Act
        // Assert
    }

    private function getRepository(): {ClassName}
    {
        $repository = static::getContainer()->get({ClassName}::class);
        assert($repository instanceof {ClassName});

        return $repository;
    }
}
```

## Feature test template

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\{Api|Web}\{Path};

use Symfony\{
    Bundle\FrameworkBundle\KernelBrowser,
    Bundle\FrameworkBundle\Test\WebTestCase
};

use PHPUnit\Framework\MockObject\MockObject;
use App\Core\Ports\{HandlerContract};
use Tests\Support\Provides\DecodesJson;

/** @coversDefaultClass \App\Presentation\{Api|Web}\{Path}\{ClassName} */
final class {ClassName}Test extends WebTestCase
{
    use DecodesJson;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test{Behavior}(): void
    {
        $handler = $this->createMock({HandlerContract}::class);
        $handler->method('handle')->willReturn([/* ... */]);

        static::getContainer()->set({HandlerContract}::class, $handler);

        $this->client->request('POST', '/route', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) json_encode([/* payload */]));

        self::assertResponseIsSuccessful();
    }
}
```

## Support helpers

Check `tests/php-unit/Support/` before writing boilerplate:

- `Provides\Persistence` - `getEntityManager()`, transaction helpers (integration tests)
- `Provides\DecodesJson` - `decodeJson()` for JSON response assertions (feature tests)
- `Provides\AssertsPersisted` - assert entity was persisted
- `Provides\DateRange` - date range helpers
- `Mocks\RateLimiterMock` - mock rate limiter in feature tests
- `Mocks\TurnstileMock` - mock Turnstile in feature tests
- `Factory\UserFactory`, `Factory\CartFactory`, `Factory\OrderFactory`, `Factory\ProductVariantFactory` - create and persist entities (integration tests)
- `Stub\UserStub` - pre-built user stubs

## Rules

1. Always use `self::assertX()`, never `$this->assertX()`
2. Mock interfaces (Contracts), never concrete classes
3. One assertion per test where possible - split into multiple tests
4. Test names: `test{WhatHappens}` - describe behavior, not implementation
5. Integration tests always wrap in a transaction and rollback in `tearDown()`
6. Feature tests inject mocks via `static::getContainer()->set(Contract::class, $mock)`

## Run tests

```bash
# Single file
cd apps/ecommerce && bin/run php vendor/bin/phpunit tests/php-unit/{Unit|Integration|Feature}/{path}

# All tests
cd apps/ecommerce && bin/run php vendor/bin/phpunit tests/php-unit
```
