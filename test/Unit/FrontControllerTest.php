<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Router;

use CodeCollab\Router\FrontController;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers CodeCollab\Router\FrontController::__construct
     * @covers CodeCollab\Router\FrontController::run
     * @covers CodeCollab\Router\FrontController::getNotFoundRoute
     * @covers CodeCollab\Router\FrontController::runRoute
     */
    public function testRunNotFound()
    {
        $dispatcher = $this->getMock('FastRoute\Dispatcher');
        $dispatcher->method('dispatch')->will($this->returnCallback(function($method, $name) {
            static $count = 0;

            if (!$count) {
                \PHPUnit_Framework_Assert::assertSame('REQUEST_METHOD', $method);
                \PHPUnit_Framework_Assert::assertSame('REQUEST_URI_PATH', $name);
            } else {
                \PHPUnit_Framework_Assert::assertSame('GET', $method);
                \PHPUnit_Framework_Assert::assertSame('/not-found', $name);
            }

            $count++;

            return [
                \FastRoute\Dispatcher::NOT_FOUND,
                [
                    (new class {
                        public function notFound() { return $response; }
                    }),
                    'notFound',
                ],
                [],
            ];
        }));

        $router = (new class($dispatcher) extends \CodeCollab\Router\Router {
            private $dispatcher;

            public function __construct($dispatcher)
            {
                $this->dispatcher = $dispatcher;
            }

            public function getDispatcher(): \FastRoute\Dispatcher {
                return $this->dispatcher;
            }
        });

        $response = (new class extends \CodeCollab\Http\Response\Response {
            public function __construct() {}

            public function send(): string {
                return 'sent!';
            }
        });

        $session = $this->getMockBuilder('CodeCollab\Http\Session\Native')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

        $auryn = $this->getMockBuilder('Auryn\Injector')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

        $injector = (new class($response) extends \CodeCollab\Router\Injector {
            private $response;

            public function __construct($response) {
                $this->response = $response;
            }

            public function execute(callable $callback, array $vars): \CodeCollab\Http\Response\Response {
                return $this->response;
            }
        });

        $request = (new class extends \CodeCollab\Http\Request\Request {
            public function __construct() {}
            public function server(string $key): string {
                return $key;
            }
        });

        $frontController = new FrontController($router, $response, $session, $injector);

        $frontController->run($request);
    }

    /**
     * @covers CodeCollab\Router\FrontController::__construct
     * @covers CodeCollab\Router\FrontController::run
     * @covers CodeCollab\Router\FrontController::runMethodNotAllowed
     * @covers CodeCollab\Router\FrontController::runRoute
     */
    public function testRunMethodNotAllowed()
    {
        $dispatcher = $this->getMock('FastRoute\Dispatcher');
        $dispatcher->method('dispatch')->will($this->returnCallback(function($method, $name) {
            static $count = 0;

            if (!$count) {
                \PHPUnit_Framework_Assert::assertSame('REQUEST_METHOD', $method);
                \PHPUnit_Framework_Assert::assertSame('REQUEST_URI_PATH', $name);
            } else {
                \PHPUnit_Framework_Assert::assertSame('GET', $method);
                \PHPUnit_Framework_Assert::assertSame('/method-not-allowed', $name);
            }

            $count++;

            return [
                \FastRoute\Dispatcher::METHOD_NOT_ALLOWED,
                [
                    (new class {
                        public function methodNotAllowed() { return $response; }
                    }),
                    'methodNotAllowed',
                ],
                [],
            ];
        }));

        $router = (new class($dispatcher) extends \CodeCollab\Router\Router {
            private $dispatcher;

            public function __construct($dispatcher)
            {
                $this->dispatcher = $dispatcher;
            }

            public function getDispatcher(): \FastRoute\Dispatcher {
                return $this->dispatcher;
            }
        });

        $response = (new class extends \CodeCollab\Http\Response\Response {
            public function __construct() {}

            public function send(): string {
                return 'sent!';
            }
        });

        $session = $this->getMockBuilder('CodeCollab\Http\Session\Native')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

        $auryn = $this->getMockBuilder('Auryn\Injector')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

        $injector = (new class($response) extends \CodeCollab\Router\Injector {
            private $response;

            public function __construct($response) {
                $this->response = $response;
            }

            public function execute(callable $callback, array $vars): \CodeCollab\Http\Response\Response {
                return $this->response;
            }
        });

        $request = (new class extends \CodeCollab\Http\Request\Request {
            public function __construct() {}
            public function server(string $key): string {
                return $key;
            }
        });

        $frontController = new FrontController($router, $response, $session, $injector);

        $frontController->run($request);
    }

    /**
     * @covers CodeCollab\Router\FrontController::__construct
     * @covers CodeCollab\Router\FrontController::run
     * @covers CodeCollab\Router\FrontController::runRoute
     */
    public function testRunFoundMatchingRoute()
    {
        $dispatcher = $this->getMock('FastRoute\Dispatcher');
        $dispatcher->method('dispatch')->will($this->returnCallback(function($method, $name) {
            \PHPUnit_Framework_Assert::assertSame('REQUEST_METHOD', $method);
            \PHPUnit_Framework_Assert::assertSame('REQUEST_URI_PATH', $name);

            return [
                \FastRoute\Dispatcher::FOUND,
                [
                    (new class {
                        public function renderThing() { return $response; }
                    }),
                    'renderThing',
                ],
                [],
            ];
        }));

        $router = (new class($dispatcher) extends \CodeCollab\Router\Router {
            private $dispatcher;

            public function __construct($dispatcher)
            {
                $this->dispatcher = $dispatcher;
            }

            public function getDispatcher(): \FastRoute\Dispatcher {
                return $this->dispatcher;
            }
        });

        $response = (new class extends \CodeCollab\Http\Response\Response {
            public function __construct() {}

            public function send(): string {
                return 'sent!';
            }
        });

        $session = $this->getMockBuilder('CodeCollab\Http\Session\Native')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

        $auryn = $this->getMockBuilder('Auryn\Injector')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

        $injector = (new class($response) extends \CodeCollab\Router\Injector {
            private $response;

            public function __construct($response) {
                $this->response = $response;
            }

            public function execute(callable $callback, array $vars): \CodeCollab\Http\Response\Response {
                return $this->response;
            }
        });

        $request = (new class extends \CodeCollab\Http\Request\Request {
            public function __construct() {}
            public function server(string $key): string {
                return $key;
            }
        });

        $frontController = new FrontController($router, $response, $session, $injector);

        $frontController->run($request);
    }
}
