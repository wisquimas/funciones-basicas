<?php
/**
 * Contiene la clase MFGroup.
 */

namespace Gafa\PropertyBuilders;

/**
 * Class MFGroup.
 * @package Gafa
 */
class MFGroup extends AbstractPropertyBuilder {

    /**
     * @var string The name of the magic field where the property value lives.
     */
    public $magicFieldId = "";

    /**
     * @var array An array of 'MFField' or 'MFArray' elements that define the sub-properties of the group.
     */
    public $subFields = array();

    /**
     * @var bool If the group is duplicable.
     */
    public $isDuplicableGroup = true;

    /**
     * @var boolean DUPLICABLE_GROUP Flag for a group that is duplicable. (value: true).
     */
    const DUPLICABLE_GROUP = true;

    /**
     * @var boolean NON_DUPLICABLE_GROUP Flag for a group that is not duplicable. (value: false).
     */
    const NON_DUPLICABLE_GROUP = false;

    /**
     * MFGroup constructor.
     *
     * @param string $propertyName Name of the property in the object where to save the value.
     * @param string $magicFieldId Id in the MF.
     * @param array $subFields An array of 'MFField' or 'MFArray' elements that define the sub-properties of the group.
     * @param boolean $isDouplicableGroup If the group is duplicable set true.
     *      When a group id duplicable, it will return an array of groups. Else, the first group will be returned.
     */
    public function __construct($propertyName, $magicFieldId, $subFields, $isDouplicableGroup)
    {
        parent::__construct($propertyName);
        $this->magicFieldId = $magicFieldId;
        $this->subFields = $subFields;
        $this->isDuplicableGroup = $isDouplicableGroup;
    }

    /**  @inheritdoc */
    public function getPropertyValue(&$wpPost, &$wpPostMeta)
    {
        $rawGroups = get_group($this->magicFieldId, $wpPost->ID);

        $groups = array();

        foreach ($rawGroups as $rawGroupKey => $rawGroup) {
            $group = new \stdClass();

            foreach ($this->subFields as $subPropertyBuilder) {
                if(!$subPropertyBuilder instanceof MFMember) {
                    throw new \Exception("Only instances that inherit from 'MFMember' are allowed to build a 'MFGroup'.");
                }
                $group->{$subPropertyBuilder->getPropertyName()} = $subPropertyBuilder->getPropertyValueFromContext($rawGroup);
            }
            $groups[] = $group;
        }

        return $this->isDuplicableGroup ? $groups : reset($groups);
    }
}