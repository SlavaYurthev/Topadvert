<?php
class SY_Topadvert_Helper_Order extends SY_Topadvert_Helper_Data {
	public function getStatus(Mage_Sales_Model_Order $_order){
		switch ($_order->getData('status')) {
			case 'complete':
				$status = 'done';
				break;

			case 'cancelled':
				$status = 'cancelled';
				break;
			
			default:
				$status = 'pending';
				break;
		}
		return $status;
	}
	public function getMoney(Mage_Sales_Model_Order $_order){
		$money = 0;
		if($this->getConfigData('type', 'money') == 1){
			$money += (int)$this->getConfigData('value', 'money');
			if($_order->getData('base_currency_code') != 'RUB'){
				$money = Mage::helper('directory')->currencyConvert(
						$money,
						$_order->getData('base_currency_code'),
						'RUB'
					);
			}
		}
		elseif($this->getConfigData('type', 'money') == 2){
			$subtotal = $_order->getData('subtotal')+0;
			if($_order->getData('order_currency_code') != 'RUB'){
				$subtotal = Mage::helper('directory')->currencyConvert(
						$subtotal,
						$_order->getData('order_currency_code'),
						'RUB'
					);
			}
			$percent = ($subtotal/100)*$this->getConfigData('value', 'money');
			$money += round($percent, 0, PHP_ROUND_HALF_UP); // number_format($percent, 0, '.', '');
		}
		return $money;
	}
}