<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Router;

use CodeCollab\Router\Injector;
use Auryn\Injector as Auryn;
use CodeCollabTest\Mock\Router\Foo;
use CodeCollabTest\Mock\Router\Bar;

class InjectorTest extends \PHPUnit_Framework_TestCase
{
    protected $injector;

    protected $object;

    public function setUp()
    {
        $this->injector = new Injector(new Auryn());

        $response = $this->getMockBuilder('CodeCollab\Http\Response\Response')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock()
        ;

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
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
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
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
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

        try {
            $this->injector->execute([$this->object, 'stringParameters'], ['foo']);
        } catch(\TypeError $e) {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }

    /**
     * @covers CodeCollab\Router\Injector::__construct
     * @covers CodeCollab\Router\Injector::execute
     * @covers CodeCollab\Router\Injector::resolveDependencies
     */
    public function testExecuteWithObjectParameters()
    {
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
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
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
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
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
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
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
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
        $this->assertInstanceof(
            'CodeCollab\Http\Response\Response',
            $this->injector->execute([$this->object, 'objectAndStringParametersStringFirstMixed'], ['foo', 'bar'])
        );
    }
}
