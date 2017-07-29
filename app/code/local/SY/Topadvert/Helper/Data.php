<?php
class SY_Topadvert_Helper_Data extends Mage_Core_Helper_Data {
	var $config; // Cacheble Variable
	public function getConfigData($key, $group = 'general'){
		// Put In Cache
		if(!$this->config){
			$this->config = Mage::getStoreConfig('topadvert_options');
		}
		return @$this->config[$group][$key];
	}
	public function getIsActive(){
		return (bool)$this->getConfigData('active');
	}
}