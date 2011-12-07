<?php
/**
 * @category   FireGento
 * @package    FireGento_DynamicCategory
 * @copyright  Copyright (c) 2011 FireGento Team (http://www.firegento.de)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->run(
    "-- DROP TABLE IF EXISTS {$this->getTable('dynamiccategory/rule')};
    CREATE TABLE IF NOT EXISTS {$this->getTable('dynamiccategory/rule')} (
      `rule_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `conditions_serialized` MEDIUMTEXT NOT NULL,
      PRIMARY KEY  (`rule_id`)
    ) ENGINE=InnoDB;
    "
);
$installer->endSetup();
