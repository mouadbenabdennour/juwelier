<?php

class Webkochshop_Kundenpreise_Model_Observer
{
	/**
	 * Anwenden der Preisanpassung des aktuellen Kunden auf der Produktliste
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function catalogProductCollectionLoadAfter($observer)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			foreach ($observer->getEvent()->getCollection() as $product)
			{
				$customer = Mage::getSingleton('customer/session')->getCustomer();
				$adjustment = $customer->getPriceAdjustment();

				/*
				 * Wenn price und final_price sich unterscheiden wird letzterer als sonderpreis angezeigt.
				 * Der Kundenrabatt muss deswegen auf beide angewendet werden.
				 */
				$finalPrice = $this->_adjustPrice($adjustment, $product->getFinalPrice(), $customer->getPriceAdjustmentType());
				$product->setFinalPrice($finalPrice);

				$price = $this->_adjustPrice($adjustment, $product->getPrice(), $customer->getPriceAdjustmentType());
				$product->setPrice($price);
			}
		}
	}

	/**
	 * Anwenden der Preisanpassung des aktuellen Kunden auf der Product Detailseite
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function catalogProductLoadAfter($observer)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$product = $observer->getEvent()->getProduct();
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$adjustment = $customer->getPriceAdjustment();

			/*
			 * Zuerst final_price setzen, da sonst bei der Berechnung der schon rabattierte "normale" preis verwendet wird
			 * und der Rabatt zwei mal angewendet wird.
			 */
			$finalPrice = $this->_adjustPrice($adjustment, $product->getFinalPrice(), $customer->getPriceAdjustmentType());
			$product->setFinalPrice($finalPrice);

			$price = $this->_adjustPrice($adjustment, $product->getPrice(), $customer->getPriceAdjustmentType());
			$product->setPrice($price);
		}
	}

	/**
	 * Berechnen der Preisanpassung nach Typ.
	 *
	 * @param float $adjustment
	 * @param float $price
	 * @param string $type 'fix' or 'percent'
	 * @return float
	 */
	protected function _adjustPrice($adjustment, $price, $type)
	{
		if ($adjustment != 0)
		{
			if ($type == Webkochshop_Kundenpreise_Model_Entity_Attribute_Source_Type::TYPE_FIX)
			{
				if ($adjustment < $price)
				{
					$price -= $adjustment;
				}
			}
			else
			{
				$price -= $price * ($adjustment / 100);
			}
		}

		return $price;
	}
}
