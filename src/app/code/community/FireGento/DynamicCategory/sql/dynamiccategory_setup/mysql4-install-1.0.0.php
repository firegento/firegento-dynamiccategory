<?php
/**
 * @category   FireGento
 * @package    FireGento_DynamicCategory
 * @copyright  Copyright (c) 2011 FireGento Team (http://www.firegento.de)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('dynamiccategory/rule')}`;
ALTER TABLE `{$this->getTable('catalog/category_product')}` ADD `dynamic` TINYINT NOT NULL;
");


$installer->addAttribute('catalog_category', 'dynamiccategory', array(
    'type'              => 'text',
    'backend'           => 'dynamiccategory/entity_attribute_backend_rule',
	'input_renderer'	=> '',
    'frontend'          => '',
    'label'             => '',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'visible_on_front'  => false,
    'unique'            => false,
));

$installer->endSetup();
