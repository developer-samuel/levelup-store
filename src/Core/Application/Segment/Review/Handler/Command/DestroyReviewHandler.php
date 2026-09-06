<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Handler\Command;

use App\Core\Domain\Segment\Review\Payload\ReviewDestroyPayload;

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Shared\Utils\Formatter\ApiResultFormatter;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Review\Handler\Command\DestroyReviewHandlerContract,
    Segment\Review\Service\Command\ReviewCommandContract,
    Shared\Logging\AppLoggerContract
};

final class DestroyReviewHandler extends AbstractCommandHandler implements DestroyReviewHandlerContract
{
    /**
     * @param SecurityPolicyContract $securityPolicy
     * @param ReviewCommandContract $reviewCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ReviewCommandContract $reviewCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param ReviewDestroyPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ReviewDestroyPayload $payload): array
    {
        return $this->execute(function () use ($payload) {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $this->reviewCommand->remove($payload->reviewId, $user);

            return ApiResultFormatter::success('Review deleted successfully.');
        });
    }
}
