<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\Middleware;

use ApiClients\Tools\TestUtilities\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use WyriHaximus\React\Http\Middleware\MiddlewareRunner;

final class MiddlewareRunnerTest extends TestCase
{
    public function testEmpty()
    {
        $runner = new MiddlewareRunner();
        /** @var ResponseInterface $response */
        $response = $this->await($runner(
            $this->prophesize(ServerRequestInterface::class)->reveal(),
            function (ServerRequestInterface $request) {
                return new Response(999);
            }
        ));

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(999, $response->getStatusCode());
    }
}
