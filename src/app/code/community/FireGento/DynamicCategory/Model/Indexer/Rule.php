<?php

class FireGento_DynamicCategory_Model_Indexer_Rule extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Indexer must be match entities
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
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
     * @var array
     */
    protected $_relatedConfigSettings = array();

    /**
     * Retrieve Special Price Status Indexer instance
     *
     * @return Flagbit_SpecialPriceStatus_Model_Status
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('dynamiccategory/rule');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('dynamiccategory')->__('Dynamic Categories');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('dynamiccategory')->__('Rule based Relation Builder');
    }


    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
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
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Category_Indexer_Flat
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
     * @param Mage_Index_Model_Event $event
     * @return Flagbit_SpecialPriceStatus_Model_Indexer_Status
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

                $reindexData  = array();
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
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        
        if (!empty($data['dynamiccategory_save_category_id'])) {
            $this->_getIndexer()->rebuildIndex(null, $data['dynamiccategory_save_category_id']);
        }
        return;

        if (!empty($data['dynamiccategory_fulltext_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['dynamiccategory_update_product_id'])) {
            $productId = $data['dynamiccategory_update_product_id'];
            $this->_getIndexer()->rebuildIndex(null, $productId);
        } else if (!empty($data['dynamiccategory_product_ids'])) {
            // mass action
            $productIds = $data['dynamiccategory_product_ids'];

            if (!empty($data['dynamiccategory_website_ids'])) {
                $websiteIds = $data['dynamiccategory_website_ids'];
                $actionType = $data['dynamiccategory_action_type'];

                foreach ($websiteIds as $websiteId) {
                    foreach (Mage::app()->getWebsite($websiteId)->getStoreIds() as $storeId) {
                        if ($actionType == 'remove') {
                            $this->_getIndexer()
                                ->cleanIndex($storeId, $productIds);
                        } else if ($actionType == 'add') {
                            $this->_getIndexer()
                                ->rebuildIndex($storeId, $productIds);
                        }
                    }
                }
            }
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $this->_getIndexer()->rebuildIndex();
    }
}
