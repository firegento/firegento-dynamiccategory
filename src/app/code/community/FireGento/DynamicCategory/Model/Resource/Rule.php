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
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 * @since     0.2.0
 */
/**
 * Rules for Conditions
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 * @since     0.2.3
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
     * Enter description here ...
     *
     * @param int $categoryId Category
     */
    public function getDynamicProductIdsByCategory($categoryId)
    {
        if (!isset($this->_dynamicProductdIdsByCategory[$categoryId])) {
            $rc = $this->getReadConnection();
            $select = $rc->select()
                ->from($this->_productCategoryTable, array('product_id'))
                ->where('category_id = ?', $categoryId)
                ->where('dynamic = ?', 1);
            $this->_dynamicProductdIdsByCategory[$categoryId] = (array) $rc->fetchCol($select);
        }
        return $this->_dynamicProductdIdsByCategory[$categoryId];
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $object
     * @param unknown_type $storeId
     * @param unknown_type $categoryIds
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
     * @param array $productIds
     * @param int   $categoryId
     * @return FireGento_DynamicCategory_Model_Mysql4_Rule
     */
    protected function _saveCategories($productIds, $categoryId)
    {
        $rc = $this->getReadConnection();
        $select = $rc->select()
            ->from($this->_productCategoryTable)
            ->where('category_id = ?', $categoryId)
            ->order('dynamic asc');

        $oldProductIds = array();
        foreach ($rc->fetchAll($select) as $category) {
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

        $write = $this->_getWriteAdapter();
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
     * Enter description here ...
     *
     * @param unknown_type $categoryIds
     * @param unknown_type $storeId
     */
    protected function _getRulesByCategoryIds($categoryIds = null, $storeId = null)
    {
        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode('catalog_category', self::RULE_ATTRIBUTE_CODE);

        $rc = $this->getReadConnection();
        $select = $rc->select()
            ->from($attribute->getBackendTable(), array('entity_id', 'value'))
            ->where('store_id = ?', $storeId === null ? 0 : $storeId)
            ->where('attribute_id = ?', $attribute->getId())
            ->where('value != \'\'');

        if ($categoryIds !== null) {
            $select->where('entity_id IN(?)', $categoryIds);
        }

        return $rc->fetchPairs($select);
    }
}
