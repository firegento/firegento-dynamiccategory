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
 * Model for rule conditions. Used as an indexer model
 * @see FireGento_DynamicCategory_Model_Indexer_Rule::getIndexer()
 *
 * @method FireGento_DynamicCategory_Model_Resource_Rule getResource()
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Rule extends Mage_CatalogRule_Model_Rule
{
    /**
     * Init the resource model
     */
    protected function _construct()
    {
        $this->_init('dynamiccategory/rule');
    }

    /**
     * Gets an instance of the respective conditions model
     *
     * @see Mage_Rule_Model_Rule::getConditionsInstance()
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
     * (1, null)    => Regenerate index for store=1
     * (1, 2)       => Regenerate index for category2 and its store=1
     * (null, 2)    => Regenerate index for all stores of category=2
     *
     * @param  int       $storeId     Store View ID to reindex
     * @param  int|array $categoryIds Category IDs to reindex
     * @return FireGento_DynamicCategory_Model_Rule Self.
     */
    public function rebuildIndex($storeId = null, $categoryIds = null)
    {
        if ($categoryIds !== null && !is_array($categoryIds)) {
            $categoryIds = array($categoryIds);
        }

        $this->setWebsiteIds(($storeId === null ? implode(',', array_keys(Mage::app()->getWebsites())) : $storeId));
        $this->getResource()->rebuildIndex($this, $storeId, $categoryIds);

        return $this;
    }

    /**
     * Initialize the rule model data from the given array.
     *
     * @param  array $rule Rule data
     * @return FireGento_DynamicCategory_Model_Rule Self.
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
     * @param array $args Arguments
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
     * @return array Matching product IDs
     */
    public function getMatchingProductIds()
    {
        $this->_productIds = array();
        $this->setCollectedAttributes(array());
        $websiteIds = explode(',', $this->getWebsiteIds());

        if ($websiteIds) {
            /* @var $productCollection Mage_Catalog_Model_Resource_Product_Collection */
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

        return $this->_productIds;
    }
}
