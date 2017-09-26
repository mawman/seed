<?php


namespace Mawman\Seed\Http\Annotations;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Mawman\Seed\Environment;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;

class RouteDiscovery extends AnnotationClassLoader
{

    private $routeCollection;

    public function __construct() {
        $reader = new AnnotationReader();
        parent::__construct($reader);
    }

    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection() {
        if (!$this->routeCollection) {
            $this->routeCollection = $this->discoverRoutes();
        }
        return $this->routeCollection;
    }

    private function discoverRoutes() {
        $dir = Environment::getInstance()->getPath('src/App/Http/Controller');
        AnnotationRegistry::registerLoader('class_exists');
        $locator = new FileLocator([$dir]);
        $annotationLoader = new AnnotationDirectoryLoader($locator, $this);
        return $annotationLoader->load($dir);
    }

    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot) {
        $route->addDefaults([
            "_controller" => [$class->getName(), $method->getName()],
        ]);
    }

}