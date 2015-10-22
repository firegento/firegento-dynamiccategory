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
 * Indexer model for the dynamic category rules
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Indexer_Rule
    extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Indexer must be match entities
     *
     * @var array Index Entities
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY  => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ),
        Mage_Catalog_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
        )
    );

    /**
     * Related Configuration Settings for match
     *
     * @var array Config Settings
     */
    protected $_relatedConfigSettings = array();

    /**
     * Retrieve Dynamic Category Rule Indexer instance
     *
     * @return FireGento_DynamicCategory_Model_Rule Indexer Model
     */
    public function getIndexer()
    {
        return Mage::getSingleton('dynamiccategory/rule');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string Indexer name
     */
    public function getName()
    {
        return Mage::helper('dynamiccategory')->__('Dynamic Categories');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string Indexer description
     */
    public function getDescription()
    {
        return Mage::helper('dynamiccategory')->__('Rule based Relation Builder');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event Index Event Model
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getEntity()) {
            case Mage_Catalog_Model_Product::ENTITY:
                //$this->_registerCatalogProductEvent($event);
                break;

            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCatalogCategoryEvent($event);
                break;
        }
    }

    /**
     * Register data required by catalog category process in event object
     *
     * @param  Mage_Index_Model_Event $event Index Event Model
     * @return FireGento_DynamicCategory_Model_Indexer_Rule Self.
     */
    protected function _registerCatalogCategoryEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $category Mage_Catalog_Model_Category */
                $category = $event->getDataObject();
                $event->addNewData('dynamiccategory_save_category_id', $category->getId());
                break;
        }

        return $this;
    }

    /**
     * Register data required by catatalog product process in event object
     *
     * @param  Mage_Index_Model_Event $event Index Event Model
     * @return FireGento_DynamicCategory_Model_Indexer_Rule
     */
    protected function _registerCatalogProductEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $product Mage_Catalog_Model_Product */
                $product = $event->getDataObject();
                $event->addNewData('dynamiccategory_update_product_id', $product->getId());
                break;

            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                /* @var $actionObject Varien_Object */
                $actionObject = $event->getDataObject();

                $reindexData = array();
                $rebuildIndex = false;

                // check changed websites
                if ($actionObject->getWebsiteIds()) {
                    $rebuildIndex = true;
                    $reindexData['dynamiccategory_website_ids'] = $actionObject->getWebsiteIds();
                    $reindexData['dynamiccategory_action_type'] = $actionObject->getActionType();
                }

                // register affected products
                if ($rebuildIndex) {
                    $reindexData['dynamiccategory_product_ids'] = $actionObject->getProductIds();
                    foreach ($reindexData as $k => $v) {
                        $event->addNewData($k, $v);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event Index Event Model
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['dynamiccategory_save_category_id'])) {
            $this->getIndexer()->rebuildIndex(null, $data['dynamiccategory_save_category_id']);
        }
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        $this->getIndexer()->rebuildIndex();
    }
}
