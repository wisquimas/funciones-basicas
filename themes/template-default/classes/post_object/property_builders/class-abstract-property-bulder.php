<?php
/**
 * Contiene la clase para el AbstractPropertyBuilder.
 */

namespace Gafa\PropertyBuilders;

/**
 * Class AbstractPropertyBuilder
 * @package Gafa
 */
abstract class AbstractPropertyBuilder {

    /**
     * @var string Name of the property.
     */
    protected $propertyName = "";

    /**
     * AbstractPropertyBuilder constructor.
     * @param string $propertyName Name of the property.
     */
    protected function __construct($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * Returns the name of the property.
     * @return string
     */
    public function getPropertyName() {
        return $this->propertyName;
    }

    /**
     * Gets the property value.
     *
     * @param \WP_Post $wpPost the wordpress post object for the object to get the value for.
     * @param array $wpPostMeta the wordpress meta data for the object to get the value for.
     * @return mixed the value to assign to the property.
     */
    public abstract function getPropertyValue(&$wpPost, &$wpPostMeta);
}