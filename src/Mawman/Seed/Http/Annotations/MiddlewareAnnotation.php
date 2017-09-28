<?php


namespace Mawman\Seed\Http\Annotations;


abstract class MiddlewareAnnotation
{

    /**
     * @var array
     */
    protected $values;

    public function __construct(array $values) {
        foreach ($this->values as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
        $this->values = $values;
    }

    /**
     * @return callable
     */
    abstract public function getHandler();

    /**
     * @return array
     */
    abstract public function getParameters();

}