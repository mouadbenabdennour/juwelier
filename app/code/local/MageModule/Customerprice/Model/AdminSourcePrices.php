<?php

class MageModule_Customerprice_Model_AdminSourcePrices
{
    public function toOptionArray()
    {
		// add group price
		if (Mage::getVersion() >= 1.7)
		{
			$prices = array(
				array('label'=>'Customerprice', 'value'=>0),
				array('label'=>'Tier Price', 'value'=>1),
				array('label'=>'Special Price', 'value'=>2),
				array('label'=>'Customer Discount', 'value'=>3),
				array('label'=>'Group Price', 'value'=>4),
			);
		}
		else
		{
			$prices = array(
				array('label'=>'Customerprice', 'value'=>0),
				array('label'=>'Tier Price', 'value'=>1),
				array('label'=>'Special Price', 'value'=>2),
				array('label'=>'Customer Discount', 'value'=>3),
			);
		}

		return $prices;
    }
}
