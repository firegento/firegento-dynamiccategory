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
 * Rules for Conditions
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 * @since     0.2.0
 */
class FireGento_DynamicCategory_Model_Rule extends Mage_Rule_Model_Rule
{
    /**
     * Gets an instance of the respective conditions model
     * 
     * @see Mage_Rule_Model_Rule::getConditionsInstance()
     * 
     * @return FireGento_DynamicCategory_Model_Rule_Condition_Combine Condition Instance
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('dynamiccategory/rule_condition_combine');
    }  
   
    /**
     * Regenerate all Stores index
     *
     * Examples:
     * (null, null) => Regenerate index for all stores
     * (1, null)    => Regenerate index for store Id=1
     * (1, 2)       => Regenerate index for product Id=2 and its store view Id=1
     * (null, 2)    => Regenerate index for all store views of product Id=2
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Flagbit_SpecialPriceStatus_Model_Status
     */
    public function rebuildIndex($storeId = null, $productId = null)
    {
        $this->getResource()->rebuildIndex($storeId, $productId);
        return $this;
    }    
    
}