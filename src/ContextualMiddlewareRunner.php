<?php declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

final class ContextualMiddlewareRunner
{
    /**
     * @var callable
     */
    private $contextChecker;

    /**
     * @var MiddlewareRunner
     */
    private $passMiddleware;

    /**
     * @var MiddlewareRunner
     */
    private $failMiddleware;

    /**
     * @param callable $contextChecker
     * @param callable[] $passMiddleware
     * @param callable[] $failMiddleware
     */
    public function __construct(callable $contextChecker, array $passMiddleware, array $failMiddleware = [])
    {
        $this->contextChecker = $contextChecker;
        $this->passMiddleware = new MiddlewareRunner(...$passMiddleware);
        $this->failMiddleware = new MiddlewareRunner(...$failMiddleware);
    }

    public function __invoke(ServerRequestInterface $request, $next)
    {
        $runner = $this->passMiddleware;
        $contextChecker = $this->contextChecker;
        if (!$contextChecker($request)) {
            $runner = $this->failMiddleware;
        }

        $response = $runner($request, $next);

        if ($response instanceof PromiseInterface) {
            return $response;
        }

        return resolve($response);
    }
}
