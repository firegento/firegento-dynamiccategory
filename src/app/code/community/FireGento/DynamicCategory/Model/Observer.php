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
 * Observer Model for adding the block to the category products
 * view and observing the category save.
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Observer
{
    /**
     * Append a custom block to the category.product.grid block.
     *
     * @param Varien_Event_Observer $observer Observer Instance
     */
    public function adminhtmlCatalogCategoryTabs(Varien_Event_Observer $observer)
    {
        /* @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $tabs = $observer->getEvent()->getTabs();

        // Add a necessary JS file
        Mage::app()->getLayout()->getBlock('head')->addJs('mage/adminhtml/rules.js');

        // Render the original products grid
        $originalBlock = Mage::app()->getLayout()
            ->createBlock('adminhtml/catalog_category_tab_product', 'category.product.grid')
            ->toHtml();

        // Render the conditions block
        $appendBlock = Mage::app()->getLayout()
            ->createBlock('dynamiccategory/adminhtml_category_dynamic', 'dynamiccategory.switcher')
            ->toHtml();

        // Remove the old tab and add the new tab
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
     * Prepare the adminhtml category products view
     *
     * @param Varien_Event_Observer $observer Observer Instance
     */
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        /* @var $block Mage_Adminhtml_Block_Widget_Grid */
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid
            && $block->getId() == 'catalog_category_products'
            && $block->getCategory()->getId()
        ) {
            $block->addColumn(
                'dynamic',
                array(
                    'header'         => Mage::helper('catalog')->__('Type'),
                    'width'          => '80',
                    'index'          => 'dynamic',
                    'sortable'       => false,
                    'filter'         => false,
                    'frame_callback' => array($this, 'decorateType')
                )
            );
        }
    }

    /**
     * Decorate Type column values
     *
     * @param  mixed         $value  Column value
     * @param  Varien_Object $row    Current Row
     * @param  Varien_Object $column Current Column
     * @return string Decorated field
     */
    public function decorateType($value, $row, $column)
    {
        $categoryId = $column->getGrid()->getCategory()->getId();
        if ($categoryId) {
            $productIds = Mage::getResourceSingleton('dynamiccategory/rule')
                ->getDynamicProductIdsByCategory($categoryId);

            if (in_array($row->getId(), $productIds)) {
                $class = 'grid-severity-major';
                $value = $column->getGrid()->__('dynamic');
            } else {
                $class = 'grid-severity-notice';
                $value = $column->getGrid()->__('static');
            }

            return '<span class="' . $class . '"><span>' . $value . '</span></span>';
        }
    }

    /**
     * Observe the category save.
     *
     * @param  Varien_Event_Observer $observer Observer Instance
     * @return FireGento_DynamicCategory_Model_Observer Self.
     */
    public function catalogCategoryPrepareSave(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        $request = $observer->getEvent()->getRequest();

        if ($request->getPost('rule')) {
            $data = $request->getPost();
            $data = $this->_filterDates($data, array('from_date', 'to_date'));

            if (isset($data['rule']['conditions'])) {
                $category->setDynamiccategory($data['rule']['conditions']);
            }
        }

        return $this;
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param  array $array      Post data
     * @param  array $dateFields Date fields
     * @return array Filtered dates
     */
    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }

        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }

        return $array;
    }
}
