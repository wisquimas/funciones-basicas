<?php
/**
 * Contiene la clase MFField.
 */

namespace Gafa\PropertyBuilders;
use Gafa\MFType;

/**
 * Class MFField.
 * @package Gafa
 */
class MFField extends MFMember {

    /**
     * @param string $propertyName Name of the property.
     * @param string $magicFieldId Name of the field in the MF.
     * @param MFType|string $magicFieldType Type of the magic field as defined in the 'MFType' class.
     */
    public function __construct($propertyName, $magicFieldId, $magicFieldType)
    {
        parent::__construct($propertyName, $magicFieldId, $magicFieldType);
    }

    /**  @inheritdoc */
    public function getPropertyValue(&$wpPost, &$wpPostMeta)
    {
            return get($this->magicFieldId, 1, 1, $wpPost->ID);
    }

    /**
     * @inheritdoc
     */
    public function getPropertyValueFromContext($rawGroup){
        // A reset is needed because MF stores internally every field as an array, even the non-duplicable ones.
        $value = reset($rawGroup[$this->magicFieldId]);
        return $this->getProcessedValue($value);
    }
}