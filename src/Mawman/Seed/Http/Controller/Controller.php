<?php


namespace Mawman\Seed\Http\Controller;


use Twig_Environment;

abstract class Controller
{

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Controller constructor.
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @param $template
     * @param null|array $parameters
     */
    protected function render($template, array $parameters = null) {
        echo $this->twig->render($template, array_merge([
            "devMode" => getenv("APP_ENV") === "development",
        ], (array)$parameters));
    }

}