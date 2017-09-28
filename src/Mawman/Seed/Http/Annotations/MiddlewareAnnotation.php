<?php


namespace Mawman\Seed\Http\Annotations;


abstract class MiddlewareAnnotation
{

    /**
     * @var array
     */
    protected $parameters;

    public function __construct(array $parameters = null) {
        $this->parameters = $parameters;
        foreach ((array) $parameters as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
    }

    /**
     * @return callable
     */
    abstract public function getHandler();

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

}