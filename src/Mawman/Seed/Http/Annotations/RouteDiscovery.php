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
        foreach (array_merge(
                     array_values($this->loadClassAnnotations($class)),
                     array_values($this->reader->getMethodAnnotations($method))
                 ) as $annotation) {
            if ($annotation instanceof MiddlewareAnnotation) {
                $defaults = $route->getDefaults();
                $route->addDefaults(['_middleware' => array_merge(
                    (isset($defaults['_middleware']) ?: []),
                    [array_merge(
                        ['_handler' => $annotation->getHandler()],
                        (array)$annotation->getParameters()
                    )]
                )]);
            }
        }
    }

    private function loadClassAnnotations(\ReflectionClass $class, $annotations = []) {
        if ($class->getParentClass()) {
            $annotations = $this->loadClassAnnotations($class->getParentClass());
        }
        return array_merge($annotations, $this->reader->getClassAnnotations($class));
    }

}