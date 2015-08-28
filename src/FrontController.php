<?php declare(strict_types=1);
/**
 * The application's front controller
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

use CodeCollab\Http\Response\Response;
use CodeCollab\Http\Session\Session;
use CodeCollab\Http\Request\Request;
use FastRoute\Dispatcher;

/**
 * The application's front controller
 *
 * @category   CodeCollab
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class FrontController
{
    /**
     * @var \CodeCollab\Router\Router The router
     */
    private $router;

    /**
     * @var \CodeCollab\Http\Response\Response The HTTP response
     */
    private $response;

    /**
     * @var \CodeCollab\Http\Session\Session The session handler
     */
    private $session;

    /**
     * @var \CodeCollab\Router\Injector The DI injector
     */
    private $injector;

    /**
     * Creates instance
     *
     * @param \CodeCollab\Router\Router          $router   The router
     * @param \CodeCollab\Http\Response\Response $response The HTTP response
     * @param \CodeCollab\Http\Session\Session   $session  The session handler
     * @param \CodeCollab\Router\Injector        $injector The DI injector
     */
    public function __construct(Router $router, Response $response, Session $session, Injector $injector)
    {
        $this->router   = $router;
        $this->response = $response;
        $this->session  = $session;
        $this->injector = $injector;
    }

    /**
     * Runs the application
     *
     * This method gets the correct route for the current request and runs the callback of the route
     *
     * @param \CodeCollab\Http\Request\Request $request The current request
     */
    public function run(Request $request)
    {
        $dispatcher = $this->router->getDispatcher();
        $routeInfo  = $dispatcher->dispatch($request->server('REQUEST_METHOD'), $request->server('REQUEST_URI_PATH'));

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $routeInfo = $this->getNotFoundRoute($dispatcher);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $routeInfo = $this->runMethodNotAllowed($dispatcher);
                break;

            case Dispatcher::FOUND:
                break;
        }

        $response = $this->runRoute($routeInfo);

        $response->send();
    }

    /**
     * Runs a route
     *
     * @param array $routeInfo The info of the active route
     *
     * @return \CodeCollab\Http\Response\Response The HTTP response
     */
    private function runRoute(array $routeInfo): Response
    {
        list($_, $callback, $vars) = $routeInfo;

        $controller = new $callback[0]($this->response, $this->session);

        return $this->injector->execute([$controller, $callback[1]], $vars);
    }

    /**
     * Gets the "not found (404)" route
     *
     * @param \FastRoute\Dispatcher $dispatcher The request dispatcher
     *
     * @return array The route
     */
    private function getNotFoundRoute(Dispatcher $dispatcher): array
    {
        return $dispatcher->dispatch('GET', '/not-found');
    }

    /**
     * Gets the "method not allowed (405)" route
     *
     * @param \FastRoute\Dispatcher $dispatcher The request dispatcher
     *
     * @return array The route
     */
    private function runMethodNotAllowed(Dispatcher $dispatcher): array
    {
        return $dispatcher->dispatch('GET', '/method-not-allowed');
    }
}
