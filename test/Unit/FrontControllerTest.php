<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Router;

use CodeCollab\Router\FrontController;
use FastRoute\Dispatcher;
use CodeCollabTest\Mock\Router\ValidController;
use CodeCollabTest\Mock\Router\NoController;
use CodeCollab\Router\Router;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Session\Native;
use CodeCollab\Router\Injector;
use CodeCollab\Http\Request\Request;
use CodeCollab\Router\ControllerNotFoundException;
use CodeCollab\Router\ActionNotFoundException;

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
        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($this->equalTo('POST'), $this->equalTo('/doesnt-exist'))
            ->willReturn([Dispatcher::NOT_FOUND, [], []])
        ;

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/not-found'))
            ->willReturn([Dispatcher::FOUND, [ValidController::class, 'action'], []])
        ;

        $router = $this->createMock(Router::class);

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->createMock(Response::class);
        $session  = $this->createMock(Native::class);
        $injector = $this->createMock(Injector::class);

        $injector
            ->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'), $this->equalTo([]))
            ->willReturn($response)
        ;

        $request = $this->createMock(Request::class);

        $request
            ->expects($this->at(0))
            ->method('server')
            ->with($this->equalTo('REQUEST_METHOD'))
            ->willReturn('POST')
        ;

        $request
            ->expects($this->at(1))
            ->method('server')
            ->with($this->equalTo('REQUEST_URI_PATH'))
            ->willReturn('/doesnt-exist')
        ;

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
        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($this->equalTo('POST'), $this->equalTo('/not-allowed'))
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, [], []])
        ;

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/method-not-allowed'))
            ->willReturn([Dispatcher::FOUND, [ValidController::class, 'action'], []])
        ;

        $router = $this->createMock(Router::class);

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->createMock(Response::class);
        $session  = $this->createMock(Native::class);
        $injector = $this->createMock(Injector::class);

        $injector
            ->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'), $this->equalTo([]))
            ->willReturn($response)
        ;

        $request = $this->createMock(Request::class);

        $request
            ->expects($this->at(0))
            ->method('server')
            ->with($this->equalTo('REQUEST_METHOD'))
            ->willReturn('POST')
        ;

        $request
            ->expects($this->at(1))
            ->method('server')
            ->with($this->equalTo('REQUEST_URI_PATH'))
            ->willReturn('/not-allowed')
        ;

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
        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/found'))
            ->willReturn([Dispatcher::FOUND, [ValidController::class, 'action'], []])
        ;

        $router = $this->createMock(Router::class);

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->createMock(Response::class);
        $session  = $this->createMock(Native::class);
        $injector = $this->createMock(Injector::class);

        $injector
            ->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'), $this->equalTo([]))
            ->willReturn($response)
        ;

        $request = $this->createMock(Request::class);

        $request
            ->expects($this->at(0))
            ->method('server')
            ->with($this->equalTo('REQUEST_METHOD'))
            ->willReturn('GET')
        ;

        $request
            ->expects($this->at(1))
            ->method('server')
            ->with($this->equalTo('REQUEST_URI_PATH'))
            ->willReturn('/found')
        ;

        $frontController = new FrontController($router, $response, $session, $injector);

        $frontController->run($request);
    }

    /**
     * @covers CodeCollab\Router\FrontController::__construct
     * @covers CodeCollab\Router\FrontController::run
     * @covers CodeCollab\Router\FrontController::runRoute
     */
    public function testRunThrowsUpOnNonExistentController()
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/found'))
            ->willReturn([Dispatcher::FOUND, [NoController::class, 'action'], []])
        ;

        $router = $this->createMock(Router::class);

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->createMock(Response::class);
        $session  = $this->createMock(Native::class);
        $injector = $this->createMock(Injector::class);
        $request  = $this->createMock(Request::class);

        $request
            ->expects($this->at(0))
            ->method('server')
            ->with($this->equalTo('REQUEST_METHOD'))
            ->willReturn('GET')
        ;

        $request
            ->expects($this->at(1))
            ->method('server')
            ->with($this->equalTo('REQUEST_URI_PATH'))
            ->willReturn('/found')
        ;

        $frontController = new FrontController($router, $response, $session, $injector);

        $this->expectException(ControllerNotFoundException::class);
        $this->expectExceptionMessage('Trying to instantiate a non existent controller (`CodeCollabTest\Mock\Router\NoController`)');

        $frontController->run($request);
    }

    /**
     * @covers CodeCollab\Router\FrontController::__construct
     * @covers CodeCollab\Router\FrontController::run
     * @covers CodeCollab\Router\FrontController::runRoute
     */
    public function testRunThrowsUpOnNonExistentAction()
    {
        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/found'))
            ->willReturn([Dispatcher::FOUND, [ValidController::class, 'noAction'], []])
        ;

        $router = $this->createMock(Router::class);

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->createMock(Response::class);
        $session  = $this->createMock(Native::class);
        $injector = $this->createMock(Injector::class);
        $request  = $this->createMock(Request::class);

        $request
            ->expects($this->at(0))
            ->method('server')
            ->with($this->equalTo('REQUEST_METHOD'))
            ->willReturn('GET')
        ;

        $request
            ->expects($this->at(1))
            ->method('server')
            ->with($this->equalTo('REQUEST_URI_PATH'))
            ->willReturn('/found')
        ;

        $frontController = new FrontController($router, $response, $session, $injector);

        $this->expectException(ActionNotFoundException::class);
        $this->expectExceptionMessage('Trying to call a non existent action (`CodeCollabTest\Mock\Router\ValidController::noAction`)');

        $frontController->run($request);
    }
}
