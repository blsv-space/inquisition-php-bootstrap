<?php

namespace App\Module\Identity\Infrastructure\Http\Controller;

use App\Module\Identity\Application\User\Service\AuthApplicationService;
use App\Module\Identity\Application\User\Service\Exception\AuthInvalidPasswordException;
use App\Module\Identity\Application\User\Service\Exception\AuthUserNotFoundException;
use Inquisition\Core\Application\Validation\Exception\ValidationException;
use Inquisition\Core\Application\Validation\HttpRequestValidator;
use Inquisition\Core\Application\Validation\Rule\NotEmptyRule;
use Inquisition\Core\Infrastructure\Http\Controller\AbstractApiController;
use Inquisition\Core\Infrastructure\Http\Request\RequestInterface;
use Inquisition\Core\Infrastructure\Http\Response\ResponseInterface;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use JsonException;

final readonly class AuthController extends AbstractApiController
{
    public const string ACTION_LOGIN = 'login';
    public const string ACTION_LOGOUT = 'logout';

    /**
     * @param RequestInterface $request
     * @param array $parameters
     * @return ResponseInterface
     * @throws JsonException
     * @throws AuthInvalidPasswordException
     * @throws AuthUserNotFoundException
     * @throws ValidationException
     * @throws PersistenceException
     */
    public function login(RequestInterface $request, array $parameters): ResponseInterface
    {
        $httpRequestValidator = new HttpRequestValidator();
        $httpRequestValidator->addRules([
            'userName' => new NotEmptyRule(),
            'password' => new NotEmptyRule(),
        ]);

        $httpRequestValidator->validate($request);

        $requestParameters = $request->getAllParameters();
        $loginResponseDTO = AuthApplicationService::getInstance()->login($requestParameters['userName'], $requestParameters['password']);

        return $this->jsonResponse($loginResponseDTO->getAsArray());
    }

    /**
     * @param RequestInterface $request
     * @param array $parameters
     * @return ResponseInterface
     * @throws JsonException
     */
    public function logout(RequestInterface $request, array $parameters): ResponseInterface
    {
        return $this->jsonResponse(['message' => 'logout']);
    }

    /**
     * @param RequestInterface $request
     * @param array $parameters
     * @return ResponseInterface
     * @throws JsonException
     */
    public function refreshToken(RequestInterface $request, array $parameters): ResponseInterface
    {
        return $this->jsonResponse(['message' => 'token']);
    }
}