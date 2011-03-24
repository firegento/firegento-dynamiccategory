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
 * @since     0.2.0
 */
/**
 * Combine Condition Class
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 * @since     0.2.0
 */
class FireGento_DynamicCategory_Model_Rule_Condition_Combine
    extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('dynamiccategory/rule_condition_combine');
    }

    /**
     * Retrieve the condition options for the select field.
     * 
     * @see Mage_Rule_Model_Condition_Abstract::getNewChildSelectOptions()
     * 
     * @return array Conditions
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                array(
                    'value' => 'dynamiccategory/rule_condition_product_found',
                    'label' => Mage::helper('dynamiccategory')->__('Product attribute combination')
                ),
            )
        );
        return $conditions;
    }
}