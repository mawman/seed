<?php


namespace Mawman\Seed;

use DI\Container;
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Environment
{

    protected static $instance;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var Container
     */
    private $container = null;

    /**
     * Environment constructor.
     * @param string $basePath
     */
    public function __construct($basePath) {
        $this->basePath = realpath($basePath);
    }

    /**
     * @param null|string|string[] $path
     * @return string
     */
    public function getPath($path = null) {
        return implode('/', array_merge([$this->basePath], (array)$path));
    }

    /**
     * @return Container
     */
    public function getContainer() {
        if (!$this->container) {
            $this->container = $this->buildContainer();
        }
        return $this->container;
    }

    protected function buildContainer() {
        $dependenciesFile = $this->getPath("src/dependencies.php");
        $builder = new ContainerBuilder();
        if (is_file($dependenciesFile)) {
            $builder->addDefinitions(include($dependenciesFile));
        }
        $builder->addDefinitions($this->defaultDependencies());
        return $builder->build();
    }

    protected function defaultDependencies() {
        return [
            Request::class => function () {
                return Request::createFromGlobals();
            },
            Session::class => function () {
                $session = new Session();
                $session->start();
                return $session;
            },
            Twig_Environment::class => function () {
                $loader = new Twig_Loader_Filesystem(static::getInstance()->getPath("src/templates"));
                $loader->addPath(__DIR__ . '/templates');
                $twig = new Twig_Environment($loader, []);
                return $twig;
            }
        ];
    }

    /**
     * @return static
     */
    public static function getInstance() {
        return static::$instance;
    }

    public static function initInstance(Environment $environment) {
        static::$instance = $environment;
    }

}