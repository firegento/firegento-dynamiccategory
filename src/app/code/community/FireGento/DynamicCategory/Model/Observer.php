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
     * 
     * 
     * @param Varien_Event_Observer $observer Observer Object
     */    
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        /*@var $block Mage_Adminhtml_Block_Widget_Grid */    
        $block = $observer->getBlock();        
        
        if($block instanceof Mage_Adminhtml_Block_Widget_Grid
            && $block->getId() == 'catalog_category_products'
            && $block->getCategory()->getId()){
                 
            $block->addColumn('dynamic', array(
                'header'    => Mage::helper('catalog')->__('Type'),
                'width'     => '80',
                'index'     => 'dynamic',
            	'sortable'		=> false,
            	'filter'		=> false,            
                'frame_callback' => array($this, 'decorateType')  
            ));
        }
    }

    /**
     * Decorate Type column values
     *
     * @return string
     */
    public function decorateType($value, $row, $column, $isExport)
    {
        $categoryId = $column->getGrid()->getCategory()->getId();
        if($categoryId){
            $productIds = Mage::getResourceSingleton('dynamiccategory/rule')->getDynamicProductIdsByCategory($categoryId);
            
            $class = '';
            if(in_array($row->getId(), $productIds)){
                $class = 'grid-severity-major';
                $value = $column->getGrid()->__('dynamic');
            }else{
                $class = 'grid-severity-notice';
                $value = $column->getGrid()->__('static');
            }
    
            return '<span class="'.$class.'"><span>'.$value.'</span></span>';
        }
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
        
        if ($request->getPost('rule')) {
            
            /*@var $model FireGento_DynamicCategory_Model_Rule */
            $model = Mage::getModel('dynamiccategory/rule');
            $data = $request->getPost();
            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            if(isset($data['rule']['conditions'])){
                $category->setDynamiccategory($data['rule']['conditions']);
            }          
        }
        return $this;
    }
   
    
    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
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
