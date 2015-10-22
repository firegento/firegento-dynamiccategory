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
 * Condition block for category edit page
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Block_Adminhtml_Category_Dynamic
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Category Model
     *
     * @var Mage_Catalog_Model_Category Category
     */
    protected $_category;

    /**
     * Retrieve the current selected category in the admin view.
     *
     * @return Mage_Catalog_Model_Category Category
     */
    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }

        return $this->_category;
    }

    /**
     * Creates the form for the condition based selection of product attributes.
     *
     * @return FireGento_DynamicCategory_Block_Adminhtml_Category_Dynamic Self.
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $data = array('conditions' => $this->getCategory()->getDynamiccategory());

        /* @var $model FireGento_DynamicCategory_Model_Rule */
        $model = Mage::getSingleton('dynamiccategory/rule');
        $model->loadPost($data);

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('dynamiccategory_');
        $form->setDataObject($this->getCategory());

        // New child url
        $newChildUrl = $this->getUrl(
            'adminhtml/dynamic/newConditionHtml/form/dynamiccategory_conditions_fieldset'
        );

        // Fieldset renderer
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('dynamiccategory/fieldset.phtml')
            ->setNewChildUrl($newChildUrl);

        // Add new fieldset
        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            array('legend' => $this->__('Dynamic Category Product Relater'))
        )->setRenderer($renderer);

        // Add new field to the fieldset
        $fieldset->addField('conditions', 'text', array(
            'name'  => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('dynamiccategory/conditions'));

        $this->setForm($form);

        return $this;
    }
}
