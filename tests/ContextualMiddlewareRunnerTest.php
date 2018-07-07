<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use ApiClients\Tools\TestUtilities\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use WyriHaximus\React\Http\Middleware\ContextualMiddlewareRunner;
use function React\Promise\resolve;

final class ContextualMiddlewareRunnerTest extends TestCase
{
    public function testRejectWithResponse()
    {
        $cmr = new ContextualMiddlewareRunner(function (ServerRequestInterface $request) {
            return false;
        }, [function () {
            $this->fail('The first middleware should never be reached');
        }]);

        /** @var ResponseInterface $response */
        $response = $this->await($cmr($this->prophesize(ServerRequestInterface::class)->reveal(), function () {
            return new Response(321);
        }));

        self::assertSame(321, $response->getStatusCode());
    }

    public function testRejectWithResponseInPromise()
    {
        $cmr = new ContextualMiddlewareRunner(function (ServerRequestInterface $request) {
            return false;
        }, [function () {
            $this->fail('The first middleware should never be reached');
        }]);

        /** @var ResponseInterface $response */
        $response = $this->await($cmr($this->prophesize(ServerRequestInterface::class)->reveal(), function () {
            return resolve(new Response(321));
        }));

        self::assertSame(321, $response->getStatusCode());
    }

    public function testFulfill()
    {
        $cmr = new ContextualMiddlewareRunner(function (ServerRequestInterface $request) {
            return true;
        }, [function (ServerRequestInterface $request, $next) {
            /** @var ResponseInterface $response */
            $response = $next($request);

            return $response->withStatus(123);
        }]);

        /** @var ResponseInterface $response */
        $response = $this->await($cmr($this->prophesize(ServerRequestInterface::class)->reveal(), function () {
            return new Response(321);
        }));

        self::assertSame(123, $response->getStatusCode());
    }
}
