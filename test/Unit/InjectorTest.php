<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Router;

use CodeCollab\Router\Injector;
use Auryn\Injector as Auryn;
use CodeCollabTest\Mock\Router\Foo;
use CodeCollabTest\Mock\Router\Bar;
use CodeCollab\Http\Response\Response;

class InjectorTest extends \PHPUnit_Framework_TestCase
{
    protected $injector;

    protected $object;

    public function setUp()
    {
        $this->injector = new Injector(new Auryn());

        $response = $this->createMock(Response::class);

        $this->object = (new class($response) {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }

            public function withoutParameters() {
                return $this->response;
            }

            public function stringParameters(string $var1, string $var2) {
                return $this->response;
            }

            public function objectParameters(Foo $var1, Bar $var2) {
                return $this->response;
            }

            public function objectAndStringParametersObjectsFirst(Foo $var1, Bar $var2, string $var3, string $var4) {
                return $this->response;
            }

            public function objectAndStringParametersStringsFirst(string $var1, string $var2, Foo $var3, Bar $var4) {
                return $this->response;
            }

            public function objectAndStringParametersObjectFirstMixed(Foo $var1, string $var2, Bar $var3, string $var4) {
                return $this->response;
            }

            public function objectAndStringParametersStringFirstMixed(string $var1, Foo $var2, string $var3, Bar $var4) {
                return $this->response;
            }
        });
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithoutParameters()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'withoutParameters'], [])
        );
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithStringParameters()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'stringParameters'], ['foo', 'bar'])
        );
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithStringParametersThrowsOnMissingParameter()
    {
        $thrown = false;

        if (version_compare(PHP_VERSION, '7.1.0a') >= 0) {
            $this->expectException(\Error::class);
        } else {
            $this->expectException(\TypeError::class);
        }

        $this->injector->execute([$this->object, 'stringParameters'], ['foo']);
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithObjectParameters()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'objectParameters'], [])
        );
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithObjectAndStringParametersObjectsFirst()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'objectAndStringParametersObjectsFirst'], ['foo', 'bar'])
        );
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithObjectAndStringParametersStringsFirst()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'objectAndStringParametersStringsFirst'], ['foo', 'bar'])
        );
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithObjectAndStringParametersObjectFirstMixed()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'objectAndStringParametersObjectFirstMixed'], ['foo', 'bar'])
        );
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithObjectAndStringParametersStringFirstMixed()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->injector->execute([$this->object, 'objectAndStringParametersStringFirstMixed'], ['foo', 'bar'])
        );
    }
}
