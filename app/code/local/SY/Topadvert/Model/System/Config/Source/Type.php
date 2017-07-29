<?php
class SY_Topadvert_Model_System_Config_Source_Type {
	public function toOptionArray(){
		$helper = Mage::helper('topadvert');
		$types = array();
		$types[] = array('label' => $helper->__('Fixed'), 'value'=> 1);
		$types[] = array('label' => $helper->__('Percent'), 'value'=> 2);
		return $types;
	}
}