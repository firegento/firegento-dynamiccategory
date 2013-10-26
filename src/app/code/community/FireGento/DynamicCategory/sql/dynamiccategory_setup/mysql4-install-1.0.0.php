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
 * @since     1.0.0
 */
/**
 * Setup Script
 *
 * @category  FireGento
 * @package   FireGento_DynamicCategory
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 * @since     1.0.0
 */

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// Perform database changes
$installer->run(
    "DROP TABLE IF EXISTS `{$this->getTable('dynamiccategory/rule')}`;
    ALTER TABLE `{$this->getTable('catalog/category_product')}` ADD `dynamic` TINYINT NOT NULL;"
);

// Create category attribute
$installer->addAttribute('catalog_category', 'dynamiccategory',
    array(
        'type' => 'text',
        'backend' => 'dynamiccategory/entity_attribute_backend_rule',
        'input_renderer' => '',
        'frontend' => '',
        'label' => '',
        'input' => '',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'visible_on_front' => false,
        'unique' => false,
    )
);

$installer->endSetup();
