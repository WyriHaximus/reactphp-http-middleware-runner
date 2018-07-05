<?php declare(strict_types=1);

namespace WyriHaximus\React\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

final class MiddlewareRunner
{
    /**
     * @var MiddlewareRunner
     */
    private $middleware;

    /**
     * @param callable[] $middleware
     */
    public function __construct(callable ...$middleware)
    {
        $this->middleware = $middleware;
    }

    public function __invoke(ServerRequestInterface $request, $next)
    {
        $response = $this->call($request, 0, $next);

        if ($response instanceof PromiseInterface) {
            return $response;
        }

        return resolve($response);
    }

    private function call(ServerRequestInterface $request, $position, $last)
    {
        if (!isset($this->middleware[$position])) {
            return $last($request);
        }

        // final request handler will be invoked without a next handler
        if (!isset($this->middleware[$position + 1])) {
            $handler = $this->middleware[$position];
            return $handler($request, $last);
        }

        $next = function (ServerRequestInterface $request) use ($position, $last) {
            return $this->call($request, $position + 1, $last);
        };

        // invoke middleware request handler with next handler
        $handler = $this->middleware[$position];
        return $handler($request, $next);
    }
}
