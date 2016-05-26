<?php
/**
 * Contiene la clase CustomField.
 */

namespace Gafa\PropertyBuilders;

/**
 * Class CustomField
 * @package Gafa
 */
class CustomField extends AbstractPropertyBuilder {

    /**
     * @var \Closure|null
     */
    public $customFunction = null;

    /**
     * @param string $propertyName Name of the property.
     * @param \Closure $customFunction Custom function used to get the value of the property.
     *      This function is guaranteed to be passed the following arguments when called.
     *          '$wpPost' WP_Post, with the wordpress post object.
     *          '$wpPostMeta' array, with the wordpress post object metadata.
     */
    public function __construct($propertyName, $customFunction)
    {
        parent::__construct($propertyName);
        $this->customFunction = $customFunction;
    }

    /**
     * Gets the property value.
     *
     * @param \WP_Post $wpPost the wordpress post object for the object to get the value for.
     * @param array $wpPostMeta the wordpress meta data for the object to get the value for.
     * @return mixed the value to assign to the property.
     */
    public function getPropertyValue(&$wpPost, &$wpPostMeta)
    {
        $customFunction = $this->customFunction;
        return $customFunction($wpPost, $wpPostMeta);
    }
}