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
 * Attribute Backend Rule
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 * @since     0.2.3
 */
class FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * After load method
     *
     * @param Varien_Object $object
     * @return void
     */
    public function afterLoad($object)
    {
        parent::afterLoad($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        $object->setData($attrCode, unserialize($object->getData($attrCode)));
    }

    /**
     * Before save method
     *
     * @param Varien_Object $object
     * @return void
     */
    public function beforeSave($object)
    {
        parent::beforeSave($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        $object->setData($attrCode, serialize($object->getData($attrCode)));
    }
}
