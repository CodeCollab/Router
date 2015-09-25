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

        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($this->equalTo('POST'), $this->equalTo('/doesnt-exist'))
            ->willReturn([\FastRoute\Dispatcher::NOT_FOUND, [], []])
        ;

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/not-found'))
            ->willReturn([\FastRoute\Dispatcher::FOUND, ['CodeCollabTest\Mock\Router\ValidController', 'action'], []])
        ;

        $router = $this->getMockBuilder('CodeCollab\Router\Router')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->getMockBuilder('CodeCollab\Http\Response\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $session = $this->getMockBuilder('CodeCollab\Http\Session\Native')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $injector = $this->getMockBuilder('CodeCollab\Router\Injector')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $injector
            ->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'), $this->equalTo([]))
            ->willReturn($response)
        ;

        $request = $this->getMockBuilder('CodeCollab\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;

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
        $dispatcher = $this->getMock('FastRoute\Dispatcher');

        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($this->equalTo('POST'), $this->equalTo('/not-allowed'))
            ->willReturn([\FastRoute\Dispatcher::METHOD_NOT_ALLOWED, [], []])
        ;

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/method-not-allowed'))
            ->willReturn([\FastRoute\Dispatcher::FOUND, ['CodeCollabTest\Mock\Router\ValidController', 'action'], []])
        ;

        $router = $this->getMockBuilder('CodeCollab\Router\Router')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->getMockBuilder('CodeCollab\Http\Response\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $session = $this->getMockBuilder('CodeCollab\Http\Session\Native')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $injector = $this->getMockBuilder('CodeCollab\Router\Injector')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $injector
            ->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'), $this->equalTo([]))
            ->willReturn($response)
        ;

        $request = $this->getMockBuilder('CodeCollab\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;

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
        $dispatcher = $this->getMock('FastRoute\Dispatcher');

        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('GET'), $this->equalTo('/found'))
            ->willReturn([\FastRoute\Dispatcher::FOUND, ['CodeCollabTest\Mock\Router\ValidController', 'action'], []])
        ;

        $router = $this->getMockBuilder('CodeCollab\Router\Router')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $router
            ->expects($this->once())
            ->method('getDispatcher')
            ->willReturn($dispatcher)
        ;

        $response = $this->getMockBuilder('CodeCollab\Http\Response\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $session = $this->getMockBuilder('CodeCollab\Http\Session\Native')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $injector = $this->getMockBuilder('CodeCollab\Router\Injector')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $injector
            ->expects($this->once())
            ->method('execute')
            ->with($this->isType('array'), $this->equalTo([]))
            ->willReturn($response)
        ;

        $request = $this->getMockBuilder('CodeCollab\Http\Request\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;

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
}
