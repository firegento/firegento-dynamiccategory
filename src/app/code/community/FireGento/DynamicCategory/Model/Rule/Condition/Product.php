<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * Product Condition Class
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 *
 * @method string getAttribute()
 */
class FireGento_DynamicCategory_Model_Rule_Condition_Product
    extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * All attribute values as array in form:
     * array(
     *   [entity_id_1] => array(
     *          [store_id_1] => store_value_1,
     *          [store_id_2] => store_value_2,
     *          ...
     *          [store_id_n] => store_value_n
     *   ),
     *   ...
     * )
     *
     * Will be set only for not global scope attribute
     *
     * @var array
     */
    protected $_entityAttributeValues = null;

    /**
     * Retrieve attribute object
     *
     * @return Varien_Object|Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeObject()
    {
        try {
            $obj = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getAttribute());
        } catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))->setFrontendInput('text');
        }

        return $obj;
    }

    /**
     * Add some special attributes to the attribute list
     *
     * @param array &$attributes Attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['attribute_set_id'] = $this->getHelper()->__('Attribute Set');
        $attributes['category_ids'] = $this->getHelper()->__('Category');
        $attributes['type_id'] = $this->getHelper()->__('Product Type');
        $attributes['created_at'] = $this->getHelper()->__('Product Created At');
        $attributes['updated_at'] = $this->getHelper()->__('Product Updated At');
    }

    /**
     * Load attribute options
     *
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product Self.
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceModel('catalog/product_attribute_collection');

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($attribute->isAllowedForRuleCondition()
                && $attribute->getDataUsingMethod('is_used_for_promo_rules')
            ) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        $this->_addSpecialAttributes($attributes);
        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Retrieve value by option
     *
     * @param  mixed $option Current Option
     * @return string Value of an Option
     */
    public function getValueOption($option = null)
    {
        if (!$this->getData('value_option')) {
            if ($this->getAttribute() === 'attribute_set_id') {
                $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();

                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter($entityTypeId)
                    ->load()
                    ->toOptionHash();

                $this->setData('value_option', $options);
            } elseif ($this->getAttribute() == 'type_id') {
                $options = Mage::getSingleton('catalog/product_type')->getOptionArray();
                $this->setData('value_option', $options);
            } elseif (is_object($this->getAttributeObject()) && $this->getAttributeObject()->usesSource()) {
                if ($this->getAttributeObject()->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }

                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions($addEmptyOption);
                $options = array();
                foreach ($optionsArr as $o) {
                    if (!is_array($o['value'])) {
                        $options[$o['value']] = $o['label'];
                    }
                }

                $this->setData('value_option', $options);
            }
        }

        return $this->getData('value_option' . (!is_null($option) ? '/' . $option : ''));
    }

    /**
     * Retrieve select option values
     *
     * @return array Select Options
     */
    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options')) {
            if ($this->getAttribute() === 'attribute_set_id') {
                $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();

                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter($entityTypeId)
                    ->load()->toOptionArray();

                $this->setData('value_select_options', $options);
            } elseif ($this->getAttribute() == 'type_id') {
                $options = Mage::getSingleton('catalog/product_type')->getOptions();
                $this->setData('value_select_options', $options);
            } elseif (is_object($this->getAttributeObject()) && $this->getAttributeObject()->usesSource()) {
                if ($this->getAttributeObject()->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }

                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions($addEmptyOption);
                $this->setData('value_select_options', $optionsArr);
            }
        }

        return $this->getData('value_select_options');
    }

    /**
     * Retrieve after element HTML
     *
     * @return string Element HTML
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger">
                <img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger"
                title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }

        return $html;
    }

    /**
     * Retrieve attribute element
     *
     * @return Varien_Data_Form_Element_Abstract Element
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * Collect validated attributes
     *
     * @param  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection Product Collection
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product Self.
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();
        if ('category_ids' != $attribute) {
            if ($this->getAttributeObject()->isScopeGlobal()) {
                $attributes = $this->getRule()->getCollectedAttributes();
                $attributes[$attribute] = true;
                $this->getRule()->setCollectedAttributes($attributes);
                $productCollection->addAttributeToSelect($attribute, 'left');
            } else {
                $this->_entityAttributeValues = $productCollection->getAllAttributeValues($attribute);
            }
        }

        return $this;
    }

    /**
     * Retrieve input type
     *
     * @return string Input Type
     */
    public function getInputType()
    {
        $selectAttributes = array('attribute_set_id', 'type_id');
        if (in_array($this->getAttribute(), $selectAttributes)) {
            return 'select';
        }

        $dateAttributes = array('created_at', 'updated_at');
        if (in_array($this->getAttribute(), $dateAttributes)) {
            return 'date';
        }

        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }

        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                $frontendInput = 'select';
                break;
            case 'multiselect':
                $frontendInput = 'multiselect';
                break;
            case 'date':
                $frontendInput = 'date';
                break;
            default:
                $frontendInput = 'string';
                break;
        }

        return $frontendInput;
    }

    /**
     * Retrieve value element type
     *
     * @return string Element Type
     */
    public function getValueElementType()
    {
        $selectAttributes = array('attribute_set_id', 'type_id');
        if (in_array($this->getAttribute(), $selectAttributes)) {
            return 'select';
        }

        $dateAttributes = array('created_at', 'updated_at');
        if (in_array($this->getAttribute(), $dateAttributes)) {
            return 'date';
        }

        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }

        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                $frontendInput = 'select';
                break;
            case 'multiselect':
                $frontendInput = 'multiselect';
                break;
            case 'date':
                $frontendInput = 'date';
                break;
            default:
                $frontendInput = 'text';
                break;
        }

        return $frontendInput;
    }

    /**
     * Retrieve value element
     *
     * @return Varien_Data_Form_Element_Abstract Element
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }
        }

        $dateAttributes = array('created_at', 'updated_at');
        if (in_array($this->getAttribute(), $dateAttributes)) {
            $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
        }

        return $element;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return boolean True/False
     */
    public function getExplicitApply()
    {
        $return = false;

        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
            case 'created_at':
            case 'updated_at':
                $return = true;
                break;
        }

        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $return = true;
                    break;
            }
        }

        return $return;
    }

    /**
     * Load array
     *
     * @param  array $arr Attribute Array
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product Self.
     */
    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        if ($attribute && $attribute->getBackendType() == 'decimal') {
            if (isset($arr['value'])) {
                $arr['value'] = Mage::app()->getLocale()->getNumber($arr['value']);
            } else {
                $arr['value'] = false;
            }

            if (isset($arr['is_value_parsed'])) {
                $arr['is_value_parsed'] = Mage::app()->getLocale()->getNumber($arr['is_value_parsed']);
            } else {
                $arr['is_value_parsed'] = false;
            }
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param  Varien_Object $object Object
     * @return boolean True/False
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($object->getAvailableInCategories());
        } elseif (!isset($this->_entityAttributeValues[$object->getId()])) {
            $attr = $object->getResource()->getAttribute($attrCode);

            if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                $this->setValue(strtotime($this->getValue()));
                $value = strtotime($object->getData($attrCode));

                return $this->validateAttribute($value);
            }

            if ($attr && $attr->getFrontendInput() == 'multiselect') {
                $value = $object->getData($attrCode);
                $value = strlen($value) ? explode(',', $value) : array();

                return $this->validateAttribute($value);
            }

            return parent::validate($object);
        } else {
            $result = false;
            $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;

            foreach ($this->_entityAttributeValues[$object->getId()] as $storeId => $value) {
                $object->setData($attrCode, $value);

                $result = parent::validate($object);
                if ($result) {
                    break;
                }
            }

            if (null === $oldAttrValue) {
                $object->unsetData($attrCode);
            } else {
                $object->setData($attrCode, $oldAttrValue);
            }

            return (bool)$result;
        }
    }

    /**
     * Retrieve the DynamicCategory helper
     *
     * @return FireGento_DynamicCategory_Helper_Data
     */
    public function getHelper()
    {
        if (!$this->hasData('dynamiccategory_helper')) {
            $this->setData('dynamiccategory_helper', Mage::helper('dynamiccategory'));
        }

        return $this->getData('dynamiccategory_helper');
    }
}
