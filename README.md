# Run a set of react/http middleware when a context checker passes

[![Build Status](https://travis-ci.org/WyriHaximus/reactphp-http-contextual-middleware-runner.svg?branch=master)](https://travis-ci.org/WyriHaximus/reactphp-http-contextual-middleware-runner)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-http-contextual-middleware-runner/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-http-contextual-middleware-runner)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-http-contextual-middleware-runner/downloads.png)](https://packagist.org/packages/WyriHaximus/react-http-contextual-middleware-runner)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-contextual-middleware-runner/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-contextual-middleware-runner/?branch=master)
[![License](https://poser.pugx.org/WyriHaximus/react-http-contextual-middleware-runner/license.png)](https://packagist.org/packages/WyriHaximus/react-http-contextual-middleware-runner)
[![PHP 7 ready](http://php7ready.timesplinter.ch/WyriHaximus/reactphp-http-middleware-clear-body/badge.svg)](https://travis-ci.org/WyriHaximus/reactphp-http-middleware-clear-body)

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/react-http-contextual-middleware-runner
```

This middleware removes the raw body from the request. Best used after the request body has been parsed.

# Usage

In the following example the [`CORS`](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS) to allow everything in 
the `/css/` and `/js/` by overwriting what comes out of [`christoph-kluge/reactphp-http-cors-middleware`](https://github.com/christoph-kluge/reactphp-http-cors-middleware).

**Note: This is an actual use case from [`WyriMaps.net`](https://www.wyrimaps.net) where there is an embeddable map 
`JS` files served from that domain.**

```php
$server = new Server([
    /** Other Middleware */
    new ContextualMiddlewareRunner(function (ServerRequestInterface $request) {
        return substr($request->getUri()->getPath(), 5) === '/css/' || substr($request->getUri()->getPath(), 4) === '/js/';
    }, function (ServerRequestInterface $request, $next) {
        /** @var ResponseInterface $response */
        $response = $next($request);
        return $response->withHeader('Access-Control-Allow-Origin','*');
    }
    new CorsMiddleware(), // Middleware from christoph-kluge/reactphp-http-cors-middleware, not shipping with this package!!!
    /** Other Middleware */
]);
```

# License

The MIT License (MIT)

Copyright (c) 2018 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
