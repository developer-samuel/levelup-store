<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Handler\Command;

use App\Core\Domain\Segment\Review\Payload\ReviewCreatePayload;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Review\Handler\Command\ReviewCommandHandlerContract,
    Segment\Review\Service\Command\ReviewCommandContract,
    Segment\Review\Service\Query\ReviewValidatorQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Utils\Formatter\ApiResultFormatter;

final class ReviewCommandHandler extends AbstractCommandHandler implements ReviewCommandHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param ReviewValidatorQueryContract $reviewValidatorQuery
     * @param ReviewCommandContract $reviewCommand
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ReviewValidatorQueryContract $reviewValidatorQuery,
        private readonly ReviewCommandContract $reviewCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param ReviewCreatePayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ReviewCreatePayload $payload): array
    {
        return $this->execute(function () use ($payload) {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $this->reviewValidatorQuery->validate($user, $payload->variantId, $payload->value);

            $this->reviewCommand->add($payload, $user);

            return ApiResultFormatter::success('Review was created successfully');
        });
    }
}
