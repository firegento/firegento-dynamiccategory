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
 * @since     0.1.0
 */
/**
 * Observer for different Magento events
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 * @since     0.1.0
 */
class FireGento_DynamicCategory_Model_Observer
{
    /**
     * adminhtmlCatalogCategoryTabs()
     * 
     * Append a custom block to the category.product.grid block.
     * 
     * @param Varien_Event_Observer $observer Observer Object
     */
    public function adminhtmlCatalogCategoryTabs(Varien_Event_Observer $observer)
    {
        $tabs = $observer->getEvent()->getTabs();

        Mage::app()->getLayout()->getBlock('head')->addJs('mage/adminhtml/rules.js');

        $originalBlock = Mage::app()->getLayout()->createBlock('adminhtml/catalog_category_tab_product', 'category.product.grid')->toHtml();
        $appendBlock = Mage::app()->getLayout()->createBlock('dynamiccategory/adminhtml_category_dynamic', 'dynamiccategory.switcher')->toHtml();

        $tabs->removeTab('products');
        $tabs->addTab(
            'products',
            array(
                'label'   => Mage::helper('catalog')->__('Category Products'),
                'content' => $originalBlock . $appendBlock,
            )
        );
    }

    /**
     * catalogCategoryPrepareSave()
     * 
     * @param Varien_Event_Observer $observer Observer Object
     * 
     * @return Flagbit_DynamicCategory_Model_Observer Self.
     */
    public function catalogCategoryPrepareSave(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        $request  = $observer->getEvent()->getRequest();

        if ($rules = $request->getPost('rule')) {
            $where = array();
            $rules = $rules['conditions'];
            foreach ($rules as $rule) {
                if (array_key_exists('attribute', $rule)) {
                    $where[$rule['attribute']][] = $rule['value'];
                }
            }

            if ($where && is_array($where)) {
                $collection = Mage::getModel('catalog/product')->getCollection();
                $collection->addAttributeToSelect('*');

                foreach ($where as $key => $val) {
                    $collection->addFieldtoFilter($key, $val);
                }

                if (count($collection->getData()) > 0) {
                    $categoryProducts = explode('&', $request->getPost('category_products'));
                    foreach ($collection->getData() as $product) {
                        // Check if product is already linked, if not add to list
                        $needle = $product['entity_id'].'=';
                        if (!$this->_arraySearchValues($needle, $categoryProducts)) {
                            $categoryProducts[] = $product['entity_id'].'=10';
                        }
                        $categoryProductsNew = implode('&', $categoryProducts);

                        // Set linked products in category model
                        $newProducts = array();
                        parse_str($categoryProductsNew, $newProducts);
                        $observer->getEvent()->getCategory()->setPostedProducts($newProducts);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * _arraySearchValues()
     * 
     * Searches for an specific needle in the values of an given haystack
     * 
     * @param string $needle   String to search
     * @param array  $haystack Search array
     * @param int    $skip     Parameter for substr.
     * 
     * @return boolean Found/Not Found
     */
    private function _arraySearchValues($needle = null, $haystack = null, $skip = 0)
    {
        if ($needle == null || $haystack == null) {
            die('$needle and $$haystack are mandatory for function _arraySearchValues()');   
        }
        foreach ($haystack as $key => $val) {
            if ($skip != 0) {
                $val = substr($val, $skip);   
            }
            if (strpos($val, $needle) !== false) {
                return true;   
            }
        }
        return false;
    }
}
