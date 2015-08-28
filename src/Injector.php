<?php declare(strict_types=1);
/**
 * Injector for controller methods
 *
 * This class is responsible for injecting a variable number of URL path arguments
 *
 * PHP version 7.0
 *
 * @category   CodeCollab
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 Pieter Hordijk <https://github.com/PeeHaa>
 * @license    See the LICENSE file
 * @version    1.0.0
 */
namespace CodeCollab\Router;

use Auryn\Injector as Auryn;
use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Response\Request;

/**
 * Injector for controller methods
 *
 * @category   CodeCollab
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class Injector
{
    /**
     * @var \Auryn\Injector Instance of an auryn injector
     */
    private $injector;

    /**
     * Creates instance
     *
     * @param \Auryn\Injector $injector Instance of an auryn injector
     */
    public function __construct(Auryn $injector)
    {
        $this->injector = $injector;
    }

    /**
     * Resolves dependencies for and executes a callback
     *
     * @param callable $callback The callback to execute
     * @param array    $vars     The variables to inject
     *
     * @return mixed The return value of the callback
     */
    public function execute(callable $callback, array $vars): Response
    {
        $arguments = $this->resolveDependencies($callback, $vars);

        return call_user_func_array($callback, $arguments);
    }

    /**
     * Resolves dependencies for a callback
     *
     * @param callable $callback The callback to execute
     * @param array    $vars     The variables to inject for string arguments (URL path variables)
     *
     * @return array List of resolved dependencies
     */
    private function resolveDependencies(callable $callback, array $vars): array
    {
        $method = new \ReflectionMethod($callback[0], $callback[1]);

        $dependencies = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getClass() === null && !count($vars)) {
                break;
            }

            if ($parameter->getClass() === null && count($vars)) {
                $dependencies[] = array_shift($vars);

                continue;
            }

            $dependencies[] = $this->injector->make($parameter->getClass()->name);
        }

        return $dependencies;
    }
}
