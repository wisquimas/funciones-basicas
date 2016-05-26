<?php
/**
 * Contiene la clase MFArray.
 */

namespace Gafa\PropertyBuilders;

use Gafa\MFType;

/**
 * Class MFArray. Use to get duplicable fields.
 *
 * Internally, MF stores duplicable fields as a group, so a 'MFField' won't work to get duplicable fields.
 *
 * @package Gafa
 */
class MFArray extends MFMember {

    /**
     * @var string The name of the MF group where the property lives.
     */
    public $magicFieldGroup = "";

    /**
     * @param string $propertyName Name of the property.
     * @param string $magicFieldGroup Name of the group in the MF.
     * @param string $magicFieldId Name of the field in the MF.
     * @param MFType|string $magicFieldType Type of the magic field as defined in the 'MFType' class.
     */
    public function __construct($propertyName, $magicFieldGroup, $magicFieldId, $magicFieldType)
    {
        parent::__construct($propertyName, $magicFieldId, $magicFieldType);
        $this->magicFieldGroup = $magicFieldGroup;
    }

    /**  @inheritdoc */
    public function getPropertyValue(&$wpPost, &$wpPostMeta)
    {
        $group = get_group($this->magicFieldGroup, $wpPost->ID);

        if(!is_array($group)) { return null; }

        // El get group siempre devuelve un array. Mas esta clase, trata con grupos no duplicables.
        // El grupo es el que no es duplicable, mas sus fields son los que son duplicables.
        $group = reset($group);

        if(!isset($group[$this->magicFieldId])) { return null; }

        $results = $group[$this->magicFieldId];

        foreach ($results as $resultKey => $result) {
            $results[$resultKey] = $this->getProcessedValue($result);
        }

        // The MF core returns an object for arrays counting from 1. We find more useful to cast it into an array
        // starting with zero, so we use this function to force cast the MF fashion to an array fashion value.
        return array_values($results);
    }

    /**
     * @inheritdoc
     */
    public function getPropertyValueFromContext($rawGroup){
        $values = $rawGroup[$this->magicFieldId];
        foreach ($values as $valueKey => $value) {
            $values[$valueKey] = $this->getProcessedValue($value);
        }
        return array_values($values);
    }
}