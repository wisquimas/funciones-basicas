<?php
/**
 * Contiene la clase MFMember.
 */

namespace Gafa\PropertyBuilders;
use Gafa\MFType;

/**
 * Class MFMember.
 * @package Gafa
 */
abstract class MFMember extends AbstractPropertyBuilder {

    /**
     * @var string The name of the magic field where the property value lives.
     */
    public $magicFieldId = "";

    /**
     * @var string The type of the MF field as defined in the 'MFType' class.
     */
    public $magicFieldType = "";

    /**
     * @param string $propertyName Name of the property.
     * @param string $magicFieldId Name of the field in the MF.
     * @param MFType|string $magicFieldType Type of the magic field as defined in the 'MFType' class.
     */
    public function __construct($propertyName, $magicFieldId, $magicFieldType)
    {
        parent::__construct($propertyName);
        $this->magicFieldId = $magicFieldId;
        $this->magicFieldType = $magicFieldType;
    }

    /**
     * Returns the property value given an entry of the result given by calling the 'get_group' MF method.
     *
     * Given a "single group entry" as returned by the 'get_group' MF function, the property builder must be capable to
     * return the property value according to the current configuration of the 'MFMember' given by the '$magicFieldId'
     * and the '$magicFieldType' properties.
     *
     * @param object|array $rawGroup
     * @return mixed
     */
    public abstract function getPropertyValueFromContext($rawGroup);

    /**
     * Processes the value of a magic field entry so a useful values gets returned.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function getProcessedValue($value){
        switch ($this->magicFieldType) {
            case MFType::ImageMedia:
                return $value["original"];
            default: break;
        }
        return $value;
    }
}