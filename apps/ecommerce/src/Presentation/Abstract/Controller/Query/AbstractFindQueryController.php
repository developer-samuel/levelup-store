<?php

declare(strict_types=1);

namespace App\Presentation\Abstract\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\Shared\Responder\ExceptionResponder;

abstract class AbstractFindQueryController extends AbstractQueryController
{
    protected object $repository;

    /**
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        SecurityProviderContract $securityProvider,
        ExceptionResponder $exceptionResponder,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityProvider,
            $exceptionResponder,
            $logger,
        );

        $this->repository = $this->getRepository();
    }

    /**
     * @return object
    */
    abstract protected function getRepository(): object;

    /**
     * Render edit page by ID
     *
     * @param int $id
     * @param string $template
     * @param string|null $redirectRoute
     * @param string $entityName
     *
     * @return Response
    */
    protected function renderFindById(
        int $id,
        string $template,
        ?string $redirectRoute = null,
        string $entityName = 'Entity',
    ): Response {
        /** @phpstan-ignore-next-line */
        $entity = $this->repository->findById($id);

        if (!$entity) {
            if ($redirectRoute !== null) {
                return $this->redirectToRoute($redirectRoute);
            }

            throw $this->createNotFoundException(sprintf('%s with ID %d not found.', $entityName, $id));
        }

        return $this->renderPage($template, [$entityName => $entity]);
    }
}
