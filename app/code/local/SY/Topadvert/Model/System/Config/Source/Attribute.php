<?php
class SY_Topadvert_Model_System_Config_Source_Attribute {
	public function toOptionArray(){
		$helper = Mage::helper('topadvert');
		$attributes = array();
		$collection = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
		if(count($collection)>0){
			foreach ($collection as $attribute) {
				$attributes[] = array(
						'label' => $attribute->getData('attribute_code'), 
						'value'=> $attribute->getData('attribute_code')
					);
			}
		}
		return $attributes;
	}
}