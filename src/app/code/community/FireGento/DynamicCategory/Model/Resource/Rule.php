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
 * Resource model for rule conditions.
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Attribute code for dynamiccategory
     */
    const RULE_ATTRIBUTE_CODE = 'dynamiccategory';

    /**
     * @var string Product Category Table name
     */
    protected $_productCategoryTable;

    /**
     * @var array Dynamic Products by Category ID
     */
    protected $_dynamicProductdIdsByCategory = array();

    /**
     * Inits the main table, the id field name and the product category table name
     */
    protected function _construct()
    {
        $this->_init('catalog/category', 'entity_id');
        $this->_productCategoryTable = $this->getTable('catalog/category_product');
    }

    /**
     * Retrieve all dynamically added product ids for the given category id
     *
     * @param int $categoryId Category
     */
    public function getDynamicProductIdsByCategory($categoryId)
    {
        if (!isset($this->_dynamicProductdIdsByCategory[$categoryId])) {
            $read = $this->getReadConnection();
            $select = $read->select()
                ->from($this->_productCategoryTable, array('product_id'))
                ->where('category_id = ?', $categoryId)
                ->where('dynamic = ?', 1);

            $this->_dynamicProductdIdsByCategory[$categoryId] = (array)$read->fetchCol($select);
        }

        return $this->_dynamicProductdIdsByCategory[$categoryId];
    }

    /**
     * Updates category-product relation, called during reindexing
     *
     * @param FireGento_DynamicCategory_Model_Rule $object Rule to reindex
     * @param null|int      $storeId     Store id for reindex
     * @param array         $categoryIds Category Ids to reindex
     */
    public function rebuildIndex($object, $storeId = null, $categoryIds = null)
    {
        $rules = $this->_getRulesByCategoryIds($categoryIds, $storeId);

        foreach ($rules as $categoryId => $rule) {
            $ruleArray = @unserialize($rule);

            $ids = array();
            if (is_array($ruleArray) && count($ruleArray) > 1) {
                $object->setData('conditions', $ruleArray);
                $object->loadPost(array('conditions' => $ruleArray));
                $ids = $object->getMatchingProductIds();
            }

            $this->_saveCategories($ids, $categoryId);
        }
    }

    /**
     * Save product category relations
     *
     * @param  array $productIds Product Ids to link with the category
     * @param  int   $categoryId Category Id
     * @return FireGento_DynamicCategory_Model_Resource_Rule
     */
    protected function _saveCategories($productIds, $categoryId)
    {
        $read = $this->getReadConnection();
        $write = $this->_getWriteAdapter();

        // Fetch the current assigned products for the given category
        $select = $read->select()
            ->from($this->_productCategoryTable)
            ->where('category_id = ?', $categoryId)
            ->order('dynamic asc');

        $oldProductIds = array();
        foreach ($read->fetchAll($select) as $category) {
            if ($category['dynamic']) {
                $oldProductIds[] = $category['product_id'];
            } elseif (
                !$category['dynamic']
                && in_array($category['product_id'], $productIds)
            ) {
                $productIds = array_diff($productIds, array($category['product_id']));
            }
        }

        $insert = array_diff($productIds, $oldProductIds);
        $delete = array_diff($oldProductIds, $productIds);

        // Insert the new category-product relations
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $productId) {
                if (empty($productId)) {
                    continue;
                }

                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'product_id'  => $productId,
                    'position'    => 99,
                    'dynamic'     => 1
                );
            }

            if ($data) {
                $write->insertMultiple($this->_productCategoryTable, $data);
            }
        }

        // Delete the old category-product relations
        if (!empty($delete)) {
            $where = join(
                ' AND ',
                array(
                    $write->quoteInto('product_id IN(?)', $delete),
                    $write->quoteInto('category_id = ?', $categoryId)
                )
            );

            $write->delete($this->_productCategoryTable, $where);
        }

        return $this;
    }

    /**
     * Fetch all rules for the given category ids and the given store id
     *
     * @param  null|array $categoryIds Category Ids
     * @param  null|int   $storeId     Store Id
     * @return array Rules array
     */
    protected function _getRulesByCategoryIds($categoryIds = null, $storeId = null)
    {
        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode('catalog_category', self::RULE_ATTRIBUTE_CODE);

        $read = $this->getReadConnection();
        $select = $read->select()
            ->from($attribute->getBackendTable(), array('entity_id', 'value'))
            ->where('store_id = ?', $storeId === null ? 0 : $storeId)
            ->where('attribute_id = ?', $attribute->getId())
            ->where('value != \'\'');

        if ($categoryIds !== null) {
            $select->where('entity_id IN(?)', $categoryIds);
        }

        return $read->fetchPairs($select);
    }
}
