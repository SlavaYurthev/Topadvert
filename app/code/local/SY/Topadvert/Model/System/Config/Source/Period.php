<?php
class SY_Topadvert_Model_System_Config_Source_Period {
	public function toOptionArray(){
		$helper = Mage::helper('topadvert');
		$types = array();
		$types[] = array('label' => $helper->__('All'), 'value'=> 'all');
		$types[] = array('label' => $helper->__('1 Day'), 'value'=> '1 Day');
		$types[] = array('label' => $helper->__('1 Week'), 'value'=> '1 Week');
		$types[] = array('label' => $helper->__('1 Month'), 'value'=> '1 Month');
		$types[] = array('label' => $helper->__('3 Month'), 'value'=> '3 Month');
		$types[] = array('label' => $helper->__('6 Month'), 'value'=> '6 Month');
		$types[] = array('label' => $helper->__('1 Year'), 'value'=> '1 Year');
		return $types;
	}
}