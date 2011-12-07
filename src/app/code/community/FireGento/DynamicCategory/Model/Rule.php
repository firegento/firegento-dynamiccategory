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
 * @version   $Id$
 * @since     0.2.0
 */
class FireGento_DynamicCategory_Model_Rule extends Mage_CatalogRule_Model_Rule
{
    /**
     *
     * Enter description here ...
     */
    protected function _construct()
    {
        $this->_init('dynamiccategory/rule');
    }

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
     * @return FireGento_DynamicCategory_Model_Rule Self.
     */
    public function rebuildIndex($storeId = null, $categoryIds = null, $productIds = null)
    {
        if($categoryIds !== null && !is_array($categoryIds)){
            $categoryIds = array($categoryIds);
        }
        $this->setWebsiteIds(($storeId === null ? implode(',', array_keys(Mage::app()->getWebsites())) : $storeId));

        $this->getResource()->rebuildIndex($this, $storeId, $categoryIds);

        return $this;
    }

    /**
     *
     * Enter description here ...
     * @param array $rule
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1]);
        }
        return $this;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
            $websiteIds = explode(',', $this->getWebsiteIds());

            if ($websiteIds) {
                $productCollection = Mage::getResourceModel('catalog/product_collection');
                $productCollection->addWebsiteFilter($websiteIds);

                $this->getConditions()->collectValidatedAttributes($productCollection);
                Mage::getSingleton('core/resource_iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('catalog/product'),
                    )
                );
            }
        }
        return $this->_productIds;
    }
}