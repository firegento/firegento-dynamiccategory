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
 * Product Found Rules Condition Class
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Rule_Condition_Product_Found
    extends FireGento_DynamicCategory_Model_Rule_Condition_Product_Combine
{
    /**
     * Init the product found conditions and set the custom type
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('dynamiccategory/rule_condition_product_found');
    }

    /**
     * Set the allowed value options for the select field.
     *
     * @see Mage_Rule_Model_Condition_Combine::loadValueOptions()
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Product_Found Self.
     */
    public function loadValueOptions()
    {
        $this->setValueOption(
            array(
                1 => Mage::helper('dynamiccategory')->__('FOUND'),
                //0 => Mage::helper('dynamiccategory')->__('NOT FOUND'),
            )
        );

        return $this;
    }

    /**
     * Returns the html code for the condition field
     *
     * @see Mage_Rule_Model_Condition_Combine::asHtml()
     *
     * @return string HTML
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml();
        $html .= Mage::helper('dynamiccategory')->__(
            'If an product is %s in the catalog with %s of these conditions true:',
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }
}
