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
class FireGento_DynamicCategory_Test_Model_Resource_Rule extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var FireGento_DynamicCategory_Model_Resource_Rule
     */
    protected $_resourceModel;

    /**
     * Initializes model under test and disables events for checking logic
     * in isolation from custom its customizations
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_resourceModel = Mage::getResourceModel('dynamiccategory/rule');
        $this->app()->disableEvents();
    }

    /**
     * Enables events back
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->app()->enableEvents();
    }

    /**
     * @loadFixture  ~FireGento_DynamicCategory/data
     * @loadFixture  testGetDynamicProductIdsByCategory
     * @dataProvider dataProvider
     */
    public function testGetDynamicProductIdsByCategory($categoryId)
    {
        $this->assertEquals(
            $this->expected('relations')->getResult(),
            $this->_resourceModel->getDynamicProductIdsByCategory($categoryId)
        );
    }

    /**
     * @loadFixture  ~FireGento_DynamicCategory/data
     * @dataProvider dataProvider
     * @depends      testGetDynamicProductIdsByCategory
     */
    public function testSaveCategories($productIds, $categoryId)
    {
        EcomDev_Utils_Reflection::invokeRestrictedMethod(
            $this->_resourceModel,
            '_saveCategories',
            array($productIds, $categoryId)
        );

        $this->assertEquals(
            $this->expected('relations')->getResult(),
            $this->_resourceModel->getDynamicProductIdsByCategory($categoryId)
        );
    }
}
