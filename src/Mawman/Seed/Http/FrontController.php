<?php


namespace Mawman\Seed\Http;


use DI\Container;
use Mawman\Seed\Http\Annotations\RouteDiscovery;
use Mawman\Seed\Http\Controller\HttpErrorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class FrontController
{

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @throws \Exception
     */
    public function handleRequest() {
        try {
            $routeInfo = $this->matchRoute();

            if (isset($routeInfo['_middleware'])) {
                $this->processMiddleware($routeInfo['_middleware']);
            }

            $this->dispatchController($routeInfo);

        } catch (ResourceNotFoundException $e) {
            $this->dispatchController([
                "_controller" => [HttpErrorController::class, "onError"],
                "code" => 404,
                "message" => "Not Found",
                "exception" => $e,
            ]);
        } catch (\Exception $e) {
            $this->dispatchController([
                "_controller" => [HttpErrorController::class, "onError"],
                "code" => 500,
                "message" => "Internal Server Error",
                "exception" => $e,
            ]);
        }
    }

    /**
     * @param $middlewareStack
     * @throws \Exception
     */
    protected function processMiddleware($middlewareStack) {
        foreach ($middlewareStack as $middleware) {
            $handler = $this->getHandler($middleware['_handler']);
            $this->container->call($handler, $middleware);
        }
    }

    /**
     * @param $routeInfo
     * @throws \Exception
     */
    protected function dispatchController($routeInfo) {
        $handler = $this->getHandler($routeInfo['_controller']);
        $this->container->call($handler, $routeInfo);
    }

    /**
     * @param $handlerInfo
     * @return callable
     * @throws \Exception
     */
    protected function getHandler($handlerInfo) {
        if ($handlerInfo instanceof \Closure) {
            return $handlerInfo->bindTo(new \stdClass());
        }
        if (is_array($handlerInfo) && count($handlerInfo) === 2) {
            return [$this->container->make($handlerInfo[0]), $handlerInfo[1]];
        }
        if (is_string($handlerInfo)) {
            return $this->container->make($handlerInfo);
        }
        throw new \Exception('Handler could not be resolved');
    }

    /**
     * @return array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function matchRoute() {
        $request = $this->container->make(Request::class);

        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);

        $routes = new RouteDiscovery();
        $routeCollection = $routes->getRouteCollection();

        $matcher = new UrlMatcher($routeCollection, $requestContext);

        return $matcher->matchRequest($request);
    }

}