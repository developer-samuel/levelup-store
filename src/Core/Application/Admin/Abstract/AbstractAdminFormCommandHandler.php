<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Abstract;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\Policy\SecurityPolicyContract,
    Shared\Logging\AppLoggerContract
};

abstract class AbstractAdminFormCommandHandler extends AbstractCommandHandler
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param AppLoggerContract $logger
    */
    public function __construct(
        protected readonly SecurityPolicyContract $securityPolicy,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @template T of array<string, mixed>
     *
     * @param callable(): T $callback
     *
     * @return T|array<string, mixed>
    */
    protected function executeAdmin(callable $callback): array
    {
        return parent::execute(function() use ($callback) {
            $this->securityPolicy->checkAdminAccess();

            return $callback();
        });
    }
}
