<?php
class SY_Topadvert_Model_Observer extends Mage_Core_Model_Abstract {
	public function beforeOrderSave($observer){
		$order = $observer->getOrder();
		$cookie = Mage::getSingleton('core/cookie');
		if($cookie->get('topadvert_pin')){
			$order->setData('topadvert_pin', $cookie->get('topadvert_pin'));
			$order->setData('topadvert_money', Mage::helper('topadvert/order')->getMoney($order));
		}
	}
	public function entry($observer){
		$helper = Mage::helper('topadvert');
		if($helper->getIsActive()){
			$action = $observer->getAction();
			if($action->getRequest()->getParam('from') == 'topadvert' && $action->getRequest()->getParam('pin')){
				$cookie = Mage::getSingleton('core/cookie');
				if(!$cookie->get('topadvert_pin')){
					$cookie->set('topadvert_pin', $action->getRequest()->getParam('pin'), time()+(86400*30*3), '/');
				}
			}
		}
	}
	public function generateYMLAfterPlaceOrder(){
		if((bool)Mage::helper('topadvert')->getConfigData('update_after_place_order', 'yml') !== false){
			$this->generateYML();
		}
	}
	public function generateYML(){
		$helper = Mage::helper('topadvert/order');
		if($helper->getIsActive()){
			$path = $helper->getConfigData('path', 'yml');
			if((bool)$path !== false){
				$path = str_replace('\\', '/', $path);
				$path = ltrim($path, '/');
				$path = Mage::getBaseDir().DS.$path;
				$rows = array('<?xml version="1.0" encoding="utf-8" ?>');
				$rows[] = '<data>';
				$collection = Mage::getModel('sales/order')->getCollection();
				if($helper->getConfigData('period', 'yml') != 'all'){
					$collection->addFieldToFilter('created_at', array(
						'gteq' => date("Y-m-d H:i:s", strtotime('-'.$helper->getConfigData('period', 'yml')))
					));
				}
				$collection->addFieldToFilter('topadvert_pin', array('notnull' => true));
				if($collection->count()>0){
					foreach ($collection as $_order) {
						$rows[] = '<order>';
						$rows[] = '<id>'.$_order->getData('increment_id').'</id>';
						$rows[] = '<pin>'.$_order->getData('topadvert_pin').'</pin>';
						$rows[] = '<status>'.$helper->getStatus($_order).'</status>';
						$rows[] = '<money>'.$_order->getData('topadvert_money').'</money>';
						$_items = $_order->getAllVisibleItems();
						if(count($_items)>0){
							$items = array();
							foreach ($_items as $_item) {
								$qty = $_item->getData('qty_ordered')+0;
								$items[] = $_item->getName().' x '.$qty.'.';
							}
							$rows[] = '<description>'.implode("\n", $items).'</description>';
						}
						$rows[] = '</order>';
					}
				}
				$rows[] = '</data>';
				$xml = implode("\n", $rows);
				@file_put_contents($path, $xml);
			}
		}
	}
	public function generatePriceList(){
		$helper = Mage::helper('topadvert');
		if($helper->getIsActive()){
			$path = $helper->getConfigData('path', 'price_list');
			if((bool)$path !== false){
				$path = str_replace('\\', '/', $path);
				$path = ltrim($path, '/');
				$path = Mage::getBaseDir().DS.$path;
				$rows = array('<?xml version="1.0" encoding="utf-8" ?>');
				$rows[] = '<yml_catalog date="'.date("Y-m-d H:i").'">';
				$rows[] = '<shop>';
				$rows[] = '<name>';
				$rows[] = Mage::getStoreConfig('general/store_information/name');
				$rows[] = '</name>';
				$rows[] = '<company>';
				$rows[] = Mage::getStoreConfig('general/store_information/name');
				$rows[] = '</company>';
				$rows[] = '<url>';
				$rows[] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
				$rows[] = '</url>';
				$currencies = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
				if(count($currencies)>0){
					$rates = Mage::getModel('directory/currency')->getCurrencyRates(
						Mage::app()->getStore()->getBaseCurrency(),
						$currencies
					);
					$rows[] = '<currencies>';
					foreach ($currencies as $currency) {
						$rows[] = '<currency id="'.$currency.'" rate="'.($rates[$currency]+0).'"/>';
					}
					$rows[] = '</currencies>';
				}
				$categories = Mage::getModel('catalog/category')->getCollection()
					->addAttributeToSelect('name')
					->addAttributeToSelect('parent_id');
				$categories->addFieldToFilter('entity_id', array('nin'=>array(1,2)));
				$categories->addFieldToFilter('is_active', true);
				if($categories->count()>0){
					$rows[] = '<categories>';
					foreach ($categories as $category) {
						$parent = '';
						if($category->getParentId() && !in_array($category->getParentId(), array(1,2))){
							$parent .= 'parentId="'.$category->getParentId().'"';
						}
						$rows[] = '<category id="'.$category->getId().'" '.$parent.'>'.$category->getName().'</category>';
					}
					$rows[] = '</categories>';
				}
				$author = Mage::getStoreConfig('topadvert_options/price_list/author');
				$isbn = Mage::getStoreConfig('topadvert_options/price_list/isbn');
				$products = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect(array('name','price','special_price','short_description',$author,$isbn));
				if($products->count()>0){
					$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
					$rows[] = '<offers>';
					foreach ($products as $product) {
						$rows[] = '<offer id="'.$product->getId().'">';
						$rows[] = '<name>'.$product->getName().'</name>';
						$rows[] = '<url>'.$product->getProductUrl().'</url>';
						$price = $product->getFinalPrice();
						if($baseCurrencyCode != 'RUB'){
							$price = Mage::helper('directory')->currencyConvert(
								$price,
								$baseCurrencyCode,
								'RUB'
							);
						}
						$price = round($price, 0, PHP_ROUND_HALF_UP); // number_format($price, 0, '.', '')
						$rows[] = '<price>'.$price.'</price>';
						$rows[] = '<currencyId>RUB</currencyId>';
						$rows[] = '<author>'.$product->getData($author).'</author>';
						$rows[] = '<ISBN>'.$product->getData($isbn).'</ISBN>';
						$rows[] = '<categoryId>'.@$product->getCategoryIds()[0].'</categoryId>';
						$rows[] = '<description>'.$product->getShortDescription().'</description>';
						$rows[] = '</offer>';
					}
					$rows[] = '</offers>';
				}
				$rows[] = '</shop>';
				$rows[] = '</yml_catalog>';
				$xml = implode("\n", $rows);
				@file_put_contents($path, $xml);
			}
		}
	}
}