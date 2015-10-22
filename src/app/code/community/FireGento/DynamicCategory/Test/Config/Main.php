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
 * PHPUnit Test Class
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * Check it the installed module has the correct module version
     */
    public function testModuleConfig()
    {
        $this->assertModuleVersion($this->expected('module')->getVersion());
        $this->assertModuleCodePool($this->expected('module')->getCodePool());

        foreach ($this->expected('module')->getDepends() as $depend) {
            $this->assertModuleIsActive('', $depend);
            $this->assertModuleDepends($depend);
        }
    }

    /**
     * Check if the block aliases are returning the correct class names
     */
    public function testBlockAliases()
    {
        $this->assertBlockAlias('dynamiccategory/adminhtml_category_dynamic', 'FireGento_DynamicCategory_Block_Adminhtml_Category_Dynamic');
        $this->assertBlockAlias('dynamiccategory/conditions', 'FireGento_DynamicCategory_Block_Conditions');
    }

    /**
     * Check if the helper aliases are returning the correct class names
     */
    public function testHelperAliases()
    {
        $this->assertHelperAlias('dynamiccategory', 'FireGento_DynamicCategory_Helper_Data');
    }

    /**
     * Check if the helper aliases are returning the correct class names
     */
    public function testModelAliases()
    {
        $this->assertModelAlias('dynamiccategory/entity_attribute_backend_rule', 'FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule');
        $this->assertModelAlias('dynamiccategory/indexer_rule', 'FireGento_DynamicCategory_Model_Indexer_Rule');
        $this->assertModelAlias('dynamiccategory/mysql4_rule', 'FireGento_DynamicCategory_Model_Mysql4_Rule');
        $this->assertModelAlias('dynamiccategory/resource_rule', 'FireGento_DynamicCategory_Model_Resource_Rule');
        $this->assertModelAlias('dynamiccategory/rule_condition_product_combine', 'FireGento_DynamicCategory_Model_Rule_Condition_Product_Combine');
        $this->assertModelAlias('dynamiccategory/rule_condition_product_found', 'FireGento_DynamicCategory_Model_Rule_Condition_Product_Found');
        $this->assertModelAlias('dynamiccategory/rule_condition_combine', 'FireGento_DynamicCategory_Model_Rule_Condition_Combine');
        $this->assertModelAlias('dynamiccategory/rule_condition_product', 'FireGento_DynamicCategory_Model_Rule_Condition_Product');
        $this->assertModelAlias('dynamiccategory/observer', 'FireGento_DynamicCategory_Model_Observer');
        $this->assertModelAlias('dynamiccategory/rule', 'FireGento_DynamicCategory_Model_Rule');

        $this->assertInstanceOf('FireGento_DynamicCategory_Model_Resource_Rule', Mage::getModel('dynamiccategory/mysql4_rule'));
    }
}
