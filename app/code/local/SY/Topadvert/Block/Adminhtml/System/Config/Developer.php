<?php
class SY_Topadvert_Block_Adminhtml_System_Config_Developer extends Mage_Adminhtml_Block_System_Config_Form_Field {
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$html = '<span style="font-weight:bold;text-transform:uppercase;">'.$element->getValue()."</span><br><br>";
		$html .= '<table>';
		$html .= '<tr><td style="padding-right:20px;">Telegram:</td><td><a href="https://telegram.me/darks_virus" target="_blank">darks_virus</a></td></tr>';
		$html .= '<tr><td>Skype:</td><td><a href="skype:darks_v1rus?chat" target="_blank">darks_v1rus</a></td></tr>';
		$html .= '</table>';
		$html .= '<br><br>По всем вопросам лучше всего писать в телеграмм ;)<br><br>';
		return $html;
	}
}