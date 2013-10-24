<?php

class MageModule_Customerprice_Model_Catalog_Price_AddCustomerPrice
	extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
	public function __construct ()
	{
	}

	public function addCustomerPrice ($input)
	{
		//Mage::log($model)html/block;
		$app = Mage::app('base');
		$form = $input['form'];
		if ($customerPrice = $form->getElement('customer_price')) {
			$customerPrice->setRenderer(
				$app->getLayout()->createBlock('customerprice_adminhtml/catalog_product_edit_tab_price_customer')
			);
		}

	}
}


	?>
