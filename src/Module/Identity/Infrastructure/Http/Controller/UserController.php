<?php

namespace App\Module\Identity\Infrastructure\Http\Controller;

use App\Module\Identity\Application\User\Service\UserApplicationService;
use Inquisition\Core\Infrastructure\Http\Controller\AbstractRestController;
use Inquisition\Core\Infrastructure\Http\Controller\RestControllerInterface;
use Inquisition\Core\Infrastructure\Http\Request\RequestInterface;
use Inquisition\Core\Infrastructure\Http\Response\ResponseInterface;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use JsonException;

final readonly class UserController extends AbstractRestController
    implements RestControllerInterface
{
    private UserApplicationService $userApplicationService;

    public function __construct()
    {
        $this->userApplicationService = UserApplicationService::getInstance();
    }

    /**
     * @param RequestInterface $request
     * @param array $parameters
     * @return ResponseInterface
     * @throws PersistenceException
     * @throws JsonException
     */
    public function index(RequestInterface $request, array $parameters): ResponseInterface
    {
        ['page' => $page, 'per_page' => $per_page] = $this->getPaginationParams($request);
        $filterParams = $this->getFilterParams($request);
        ['field' => $field, 'direction' => $direction] = $this->getSortParams($request);;
        $offset = ($page - 1) * $per_page;

        $users = $this->userApplicationService->getUsersBy(
            criteria: $filterParams,
            orderBy: [$field => $direction],
            limit: $per_page,
            offset: $offset,
        );

        $normalizeData = $this->normalizeData($users);
        $total = $this->userApplicationService->countUsersBy($filterParams);

        return $this->jsonPaginatedResponse(
            data: $normalizeData,
            total: $total,
            page: $page,
            perPage: $per_page,
        );
    }

}