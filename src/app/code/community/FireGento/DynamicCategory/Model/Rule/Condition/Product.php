<?php
/**                                                                       
 * This file is part of the FIREGENTO project.
 * 
 * FireGento_DynamicCategory is free software; you can redistribute it and/or 
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
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 * @since     0.2.1
 */
/**
 * Condition Class for Product Attributes and Products
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 * @since     0.2.1
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
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product Self.
     */
    public function getAttributeObject()
    {
        try {
            $obj = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))->setFrontendInput('text');
        }
        return $obj;
    }

    /**
     * Add special attributes
     *
     * @param array &$attributes Attributes
     * 
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['attribute_set_id'] = Mage::helper('dynamiccategory')->__('Attribute Set');
        $attributes['category_ids']     = Mage::helper('dynamiccategory')->__('Category');
    }

    /**
     * Load attribute options
     *
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product Self.
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesByCode();
        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if ($attribute->getData('is_visible')) {
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
     * @param mixed $option
     * 
     * @return string Value of an Option
     */
    public function getValueOption($option=null)
    {
        if (!$this->getData('value_option')) {
            if ($this->getAttribute()==='attribute_set_id') {
                $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();

                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter($entityTypeId)
                    ->load()
                    ->toOptionHash();
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
                    if (is_array($o['value'])) {
                        // Do nothing
                    } else {
                        $options[$o['value']] = $o['label'];
                    }
                }
                $this->setData('value_option', $options);
            }
        }
        return $this->getData('value_option'.(!is_null($option) ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values
     *
     * @return array Select Options
     */
    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options')) {
            if ($this->getAttribute()==='attribute_set_id') {
                $entityTypeId = Mage::getSingleton('eav/config')
                    ->getEntityType('catalog_product')->getId();
                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter($entityTypeId)
                    ->load()->toOptionArray();
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
            case 'sku': case 'category_ids':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger" title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }
        return $html;
    }

    /**
     * Retrieve attribute element
     *
     * @return Varien_Form_Element_Abstract Element
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
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection Collection
     * 
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
        if ($this->getAttribute()==='attribute_set_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'string';
        }
    }

    /**
     * Retrieve value element type
     *
     * @return string Element Type
     */
    public function getValueElementType()
    {
        if ($this->getAttribute()==='attribute_set_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
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
        return $element;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return boolean True/False
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                return true;
        }
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }
        return false;
    }

    /**
     * Load array
     *
     * @param array $arr Attribute Array
     * 
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product Self.
     */
    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        if ($attribute && $attribute->getBackendType() == 'decimal') {
            $arr['value'] = isset($arr['value']) ? Mage::app()->getLocale()->getNumber($arr['value']) : false;
            $arr['is_value_parsed'] = isset($arr['is_value_parsed']) ? Mage::app()->getLocale()->getNumber($arr['is_value_parsed']) : false;
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param Varien_Object $object Object
     * 
     * @return boolean True/False
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($object->getAvailableInCategories());
        } elseif (! isset($this->_entityAttributeValues[$object->getId()])) {
            $attr = $object->getResource()->getAttribute($attrCode);

            if ($attr && $attr->getBackendType() == 'datetime' && ! is_int($this->getValue())) {
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

                $result |= parent::validate($object);

                if ($result) {
                    break;
                }
            }

            if (is_null($oldAttrValue)) {
                $object->unsetData($attrCode);
            } else {
                $object->setData($attrCode, $oldAttrValue);
            }

            return (bool) $result;
        }
    }
}