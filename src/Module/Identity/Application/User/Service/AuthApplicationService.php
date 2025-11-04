<?php

namespace App\Module\Identity\Application\User\Service;

use App\Module\Identity\Application\User\Service\Exception\AuthInvalidPasswordException;
use App\Module\Identity\Application\User\Service\Exception\AuthUserNotFoundException;
use App\Module\Identity\Domain\RefreshToken\ValueObject\Token;
use App\Module\Identity\Domain\User\DTO\LoginResponseDTO;
use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\Service\AuthDomainService;
use App\Module\Identity\Domain\User\Service\UserDomainService;
use App\Module\Identity\Domain\User\ValueObject\UserName;
use App\Shared\Infrastructure\Security\Exception\JwtInvalidTokenException;
use App\Shared\Infrastructure\Security\Exception\JwtTokenExpiredException;
use DateMalformedIntervalStringException;
use Inquisition\Core\Application\Service\ApplicationServiceInterface;
use Inquisition\Core\Infrastructure\Http\Router\RequestDispatcher;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Inquisition\Foundation\Singleton\SingletonTrait;

class AuthApplicationService
    implements ApplicationServiceInterface
{
    use SingletonTrait;

    private AuthDomainService $authDomainService;
    private UserDomainService $userDomainService;
    private RequestDispatcher $requestDispatcher;
    private ?User $authUser = null;

    public function __construct()
    {
        $this->authDomainService = AuthDomainService::getInstance();
        $this->userDomainService = UserDomainService::getInstance();
        $this->requestDispatcher = RequestDispatcher::getInstance();
    }

    /**
     * @param string $username
     * @param string $password
     * @return LoginResponseDTO
     * @throws AuthInvalidPasswordException
     * @throws AuthUserNotFoundException
     * @throws DateMalformedIntervalStringException
     * @throws PersistenceException
     */
    public function login(string $username, string $password): LoginResponseDTO
    {
        $userName = UserName::fromRaw($username);
        $user = $this->userDomainService->findUserByUsername($userName);
        if (!$user) {
            throw new AuthUserNotFoundException($userName->toRaw());
        }

        if (!$this->authDomainService->verifyPassword($user, $password)) {
            throw new AuthInvalidPasswordException($userName->toRaw());
        }

        return $this->authDomainService->login($user);
    }

    /**
     * @return void
     * @throws PersistenceException
     */
    public function logout(): void
    {
        $user = $this->authUser();
        if (!$user) {
            return;
        }

        $this->authDomainService->logout($user);
        $this->authUser = null;
    }

    /**
     * @param Token|null $token
     * @param bool|null $disableCache
     * @return User|null
     * @throws PersistenceException
     */
    public function authUser(?Token $token = null, ?bool $disableCache = false): ?User
    {
        if ($this->authUser && !$disableCache) {
            return $this->authUser;
        }

        if (!$token) {
            $token = $this->extractTokenFromRequest();
        }

        if (!$token) {
            return null;
        }

        try {
            return $this->authDomainService->authByJwtToken($token);
        } catch (JwtInvalidTokenException|JwtTokenExpiredException $_) {
            return null;
        }
    }

    /**
     * Extract JWT token from current request
     *
     * @return string|null
     */
    private function extractTokenFromRequest(): ?string
    {
        $request = $this->requestDispatcher->request;
        if (!$request) {
            return null;
        }
        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        return Token::fromRaw(str_replace('Bearer ', '', $authHeader));
    }
}