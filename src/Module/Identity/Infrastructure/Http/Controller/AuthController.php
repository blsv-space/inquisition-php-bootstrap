<?php

namespace App\Module\Identity\Infrastructure\Http\Controller;

use Inquisition\Core\Infrastructure\Http\Controller\AbstractApiController;
use Inquisition\Core\Infrastructure\Http\Request\RequestInterface;
use Inquisition\Core\Infrastructure\Http\Response\ResponseInterface;
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
     */
    public function login(RequestInterface $request, array $parameters): ResponseInterface
    {
        return $this->jsonResponse(['token' => 'token']);
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
}