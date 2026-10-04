<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Handler\Command;

use App\Core\Domain\{
    Segment\Review\Payload\ReviewRatingPayload
};

use App\Core\Application\Abstract\Handler\AbstractCommandHandler;

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Review\Handler\Command\ToggleReviewRatingHandlerContract,
    Segment\Review\Service\Command\ReviewRatingCommandContract,
    Shared\Logging\AppLoggerContract
};

final class ToggleReviewRatingHandler extends AbstractCommandHandler implements ToggleReviewRatingHandlerContract
{
    public function __construct(
        private readonly SecurityPolicyContract $securityPolicy,
        private readonly ReviewRatingCommandContract $reviewRatingCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /** @return array<string, mixed> */
    public function handle(ReviewRatingPayload $payload): array
    {
        return $this->execute(function () use ($payload) {
            $user = $this->securityPolicy->checkIfEmailVerified();

            $result = $this->reviewRatingCommand->toggle(
                $payload->reviewId,
                $user,
                $payload->type,
            );

            return ['exists' => $result];
        });
    }
}
