<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->addAttribute("order", "topadvert_pin", array("type"=>"varchar"));
$setup->addAttribute("order", "topadvert_money", array("type"=>"varchar"));

$installer->endSetup();