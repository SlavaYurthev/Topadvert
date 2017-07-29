<?php
class SY_Topadvert_Model_System_Config_Source_Frequency {
	public function toOptionArray(){
		$helper = Mage::helper('topadvert');
		$expressions = array();
		$expressions[] = array('label' => $helper->__('Once per Minute'), 'value'=> '* * * * *');
		$expressions[] = array('label' => $helper->__('Once per Hour'), 'value'=> '0 * * * *');
		$expressions[] = array('label' => $helper->__('Once per Day'), 'value'=> '0 0 * * *');
		$expressions[] = array('label' => $helper->__('Once per Week'), 'value'=> '0 0 * * 0');
		$expressions[] = array('label' => $helper->__('Once per Month'), 'value'=> '0 0 1 * *');
		return $expressions;
	}
}