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
 * Rule Backend Model for the attribute
 *
 * @category FireGento
 * @package  FireGento_DynamicCategory
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * After load method
     *
     * @param  Varien_Object $object Object Model
     * @return FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule Self.
     */
    public function afterLoad($object)
    {
        parent::afterLoad($object);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $object->setData($attrCode, unserialize($object->getData($attrCode)));

        return $this;
    }

    /**
     * Before save method
     *
     * @param  Varien_Object $object Object Model
     * @return FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule Self.
     */
    public function beforeSave($object)
    {
        parent::beforeSave($object);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $object->setData($attrCode, serialize($object->getData($attrCode)));

        return $this;
    }

    /**
     * Always update the related products after save.
     *
     * @param  Varien_Object $object Object Model
     * @return FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule Self.
     */
    public function afterSave($object)
    {
        $object->setIsChangedProductList(true);

        return parent::afterSave($object);
    }
}
