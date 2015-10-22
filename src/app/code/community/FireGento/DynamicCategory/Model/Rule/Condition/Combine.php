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
 * Combine Condition Class
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Rule_Condition_Combine
    extends Mage_CatalogRule_Model_Rule_Condition_Combine
{
    /**
     * @var array
     */
    protected $_customFilterAttributes = array(
        'type_id', 'created_at', 'updated_at'
    );

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('dynamiccategory/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            // Use CatalogRule condition class by default
            $class = 'catalogrule/rule_condition_product';

            // Use DynamicCategory condition class for some attributes
            if (in_array($code, $this->_customFilterAttributes)) {
                $class = 'dynamiccategory/rule_condition_product';
            }

            $attributes[] = array(
                'value' => $class . '|' . $code,
                'label' => $label
            );
        }

        $conditions = array(
            array(
                'value' => '',
                'label' => Mage::helper('rule')->__('Please choose a condition to add...')
            ),
            array(
                'value' => 'catalogrule/rule_condition_combine',
                'label' => Mage::helper('catalogrule')->__('Conditions Combination')
            ),
            array(
                'label' => Mage::helper('catalogrule')->__('Product Attribute'),
                'value' => $attributes
            ),
        );

        return $conditions;
    }

    /**
     * Returns the aggregator options
     *
     * @see Mage_Rule_Model_Condition_Combine::loadAggregatorOptions()
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Combine Self.
     */
    public function loadAggregatorOptions()
    {
        $this->setAggregatorOption(
            array(
                'all' => Mage::helper('rule')->__('ALL'),
                //'any' => Mage::helper('rule')->__('ANY'),
            )
        );

        return $this;
    }

    /**
     * Returns the value options
     *
     * @see Mage_Rule_Model_Condition_Combine::loadValueOptions()
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Combine Self.
     */
    public function loadValueOptions()
    {
        $this->setValueOption(
            array(
                1 => Mage::helper('rule')->__('TRUE'),
                //0 => Mage::helper('rule')->__('FALSE'),
            )
        );

        return $this;
    }
}
