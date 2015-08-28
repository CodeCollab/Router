<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Router;

use CodeCollab\Router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    protected $router;

    public function setUp()
    {
        $routeCollector = $this->getMockBuilder('FastRoute\RouteCollector')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $routeCollector->method('getData')->willReturn(['foo' => 'bar']);

        $this->router = new Router(
            $routeCollector,
            function($data) {
                \PHPUnit_Framework_Assert::assertSame(['foo' => 'bar'], $data);

                return $this->getMock('FastRoute\Dispatcher');
            },
            TEST_DATA_DIR . '/cache/routes.php'
        );
    }

    public function tearDown()
    {
        @unlink(TEST_DATA_DIR . '/cache/routes.php');
    }

    /**
     * @covers CodeCollab\Router\Router::__construct
     * @covers CodeCollab\Router\Router::addRoute
     * @covers CodeCollab\Router\Router::get
     */
    public function testGetReturnsSelf()
    {
        $this->assertInstanceOf('CodeCollab\Router\Router', $this->router->get('/', []));
    }

    /**
     * @covers CodeCollab\Router\Router::__construct
     * @covers CodeCollab\Router\Router::addRoute
     * @covers CodeCollab\Router\Router::post
     */
    public function testPostReturnsSelf()
    {
        $this->assertInstanceOf('CodeCollab\Router\Router', $this->router->post('/', []));
    }

    /**
     * @covers CodeCollab\Router\Router::__construct
     * @covers CodeCollab\Router\Router::addRoute
     */
    public function testAddRouteReturnsSelf()
    {
        $this->assertInstanceOf('CodeCollab\Router\Router', $this->router->addRoute('CUSTOMVERB', '/', []));
    }

    /**
     * @covers CodeCollab\Router\Router::__construct
     * @covers CodeCollab\Router\Router::addRoute
     * @covers CodeCollab\Router\Router::get
     * @covers CodeCollab\Router\Router::buildCache
     * @covers CodeCollab\Router\Router::getDispatcher
     */
    public function testGetDispatcherNewFile()
    {
        $this->assertFalse(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        $this->assertInstanceOf('FastRoute\Dispatcher', $this->router->get('/', [])->getDispatcher());

        $this->assertTrue(file_exists(TEST_DATA_DIR . '/cache/routes.php'));
    }

    /**
     * @covers CodeCollab\Router\Router::__construct
     * @covers CodeCollab\Router\Router::addRoute
     * @covers CodeCollab\Router\Router::get
     * @covers CodeCollab\Router\Router::buildCache
     * @covers CodeCollab\Router\Router::getDispatcher
     */
    public function testGetDispatcherExistingFileDoNotReload()
    {
        $routeCollector = $this->getMockBuilder('FastRoute\RouteCollector')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $routeCollector->method('getData')->willReturn(['foo' => 'bar']);

        $router = new Router(
            $routeCollector,
            function($data) {
                \PHPUnit_Framework_Assert::assertSame(['foo' => 'baz'], $data);

                return $this->getMock('FastRoute\Dispatcher');
            },
            TEST_DATA_DIR . '/cache/routes.php'
        );

        $this->assertFalse(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        copy(TEST_DATA_DIR . '/routes.php', TEST_DATA_DIR . '/cache/routes.php');

        $this->assertTrue(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        $this->assertInstanceOf('FastRoute\Dispatcher', $router->get('/', [])->getDispatcher());

        $this->assertTrue(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        $routes = require TEST_DATA_DIR . '/cache/routes.php';

        $this->assertSame(['foo' => 'baz'], $routes);
    }

    /**
     * @covers CodeCollab\Router\Router::__construct
     * @covers CodeCollab\Router\Router::addRoute
     * @covers CodeCollab\Router\Router::get
     * @covers CodeCollab\Router\Router::buildCache
     * @covers CodeCollab\Router\Router::getDispatcher
     */
    public function testGetDispatcherExistingFileDoReload()
    {
        $routeCollector = $this->getMockBuilder('FastRoute\RouteCollector')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $routeCollector->method('getData')->willReturn(['foo' => 'bar']);

        $router = new Router(
            $routeCollector,
            function($data) {
                \PHPUnit_Framework_Assert::assertSame(['foo' => 'bar'], $data);

                return $this->getMock('FastRoute\Dispatcher');
            },
            TEST_DATA_DIR . '/cache/routes.php',
            true
        );

        $this->assertFalse(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        copy(TEST_DATA_DIR . '/routes.php', TEST_DATA_DIR . '/cache/routes.php');

        $this->assertTrue(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        $this->assertInstanceOf('FastRoute\Dispatcher', $router->get('/', [])->getDispatcher());

        $this->assertTrue(file_exists(TEST_DATA_DIR . '/cache/routes.php'));

        $routes = require TEST_DATA_DIR . '/cache/routes.php';

        $this->assertSame(['foo' => 'bar'], $routes);
    }
}
