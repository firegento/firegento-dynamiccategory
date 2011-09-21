<?php 
class FireGento_DynamicCategory_Model_Entity_Attribute_Backend_Rule extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
   
    public function afterLoad($object)
    {
        parent::afterLoad($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        $object->setData($attrCode, unserialize($object->getData($attrCode)));
    }

    public function beforeSave($object)
    {
        parent::beforeSave($object);
        $attrCode = $this->getAttribute()->getAttributeCode();
        $object->setData($attrCode, serialize($object->getData($attrCode)));
    }    
	
}
