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
 * Product Combine Rules Condition Class
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Rule_Condition_Product_Combine
    extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Init the product combine conditions and set the custom type
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('dynamiccategory/rule_condition_product_combine');
    }

    /**
     * Retrieve the product options for the select field.
     *
     * @see Mage_Rule_Model_Condition_Abstract::getNewChildSelectOptions()
     * @return array Conditions as array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('dynamiccategory/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $pAttributes = array();
        $iAttributes = array();
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = array(
                    'value' => 'dynamiccategory/rule_condition_product|' . $code,
                    'label' => $label
                );
            } else {
                $pAttributes[] = array(
                    'value' => 'dynamiccategory/rule_condition_product|' . $code,
                    'label' => $label
                );
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                array(
                    'value' => 'dynamiccategory/rule_condition_product_combine',
                    'label' => Mage::helper('dynamiccategory')->__('Conditions Combination')
                ),
                array(
                    'label' => Mage::helper('dynamiccategory')->__('Product Attribute'),
                    'value' => $pAttributes
                ),
            )
        );

        return $conditions;
    }
}
