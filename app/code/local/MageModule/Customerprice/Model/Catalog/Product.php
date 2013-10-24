<?php

class MageModule_Customerprice_Model_Catalog_Product extends Mage_Catalog_Model_Product
{

	private static function _sortPrices($a, $b)
	{
		return $a['price_qty'] - $b['price_qty'];
	}

	public function getTierPrice($qty = null)
	{
		// get original tier prices
		$tier_price = parent::getTierPrice($qty);

		// get original price
		$price = $this->getPrice();

		// get customer id
		$customer_id = Mage::Helper('customerprice/productprice')->customerPriceGetCustomerId();

		// get product
		$product = $this->load($this->getId());

		// no customer price if its not a simple product
		if ($product->getSuperProduct())// || $product->getSuperProduct()->isConfigurable)
			return $tier_price;

		// no customer id so return tier prices
		if ($customer_id == "")
			return $tier_price;

		// get the customer object
		$customerObject = Mage::getModel('customer/customer')->load($customer_id);

		// get customer prices
		$customerPrices = Mage::Helper('customerprice/productprice')
			->customerPricesGetValid($product->getData('customer_price'), $customer_id);

		// check if price sorting is enabled
		$found = false;

		if (Mage::getStoreConfig('customer_price/price_sorting_order/enable_sort_prices') == 1)
		{
			for ($i=1; $i<6; $i++)
			{
				$prices = Mage::Helper('customerprice/productprice')->getConfValue(null);
				$fprice = null;
				$price_id = (int)Mage::getStoreConfig('customer_price/price_sorting_order/sort_price_' . $i);

				foreach ($prices as $k => $v)
				{
					if ($v == $price_id)
					{
						// check for groupprice since 1.7
						if (Mage::getVersion() >= 1.7)
						{
							if ($k == 'group_price')
							{
								$group_price = parent::getGroupPrice();
								if ($group_price)
								{
									return $group_price;
								}
							}
						}

						//return tier price
						if ($k == 'tier_price')
						{
							if (count($tier_price))
							{
								return $tier_price;
							}
						}

						// continue to the normal customer price stuff
						if ($k == 'customer_price')
						{
							if (count($customerPrices))
							{
								if ($qty)
								{
									$return_price = Mage::Helper('customerprice/productprice')->customerPriceGetQuantity($customerPrices,$qty);
									if (count($return_price))
										return $return_price['website_price'];
									else
										return $price;
								}
								return $customerPrices;
							}
						}

						// special price
						if ($k == 'special_price')
						{
							if (parent::getSpecialPrice())
								return parent::getSpecialPrice();
						}

						// customer discount
						if ($k == 'customer_discount')
						{
							if ($customerObject->getDiscount() && $product->getDiscountable() === "1")
								return ($price / 100 ) * (100 - $customerObject->getDiscount());
						}
					}
				}
			}

			// if no price matches return standard price
			if ($qty)
			{
				return $price;
			}
			else
			{
				return array();
			}
		}

		// get the cheapest price

		// join tier and group prices
		if (is_array($tier_price))
		{
			$customerPrices = array_merge($customerPrices, $tier_price);
			// only sort in 1.4 and 1.5 -- scrap that, just sort for all
			//if (Mage::getVersion() >= 1.4 && Mage::getVersion() <= 1.6)
			usort($customerPrices, array('MageModule_Customerprice_Model_Catalog_Product', '_sortPrices'));
		}

		if ($qty)
		{
			$customerPrices = Mage::Helper('customerprice/productprice')->customerPriceGetQuantity($customerPrices,$qty);
			$customerPrices = $customerPrices['website_price'];

			if ($customerObject->getDiscount() && $product->getDiscountable() === "1")
			{
				$dprice =  ($price / 100 ) * (100 - $customerObject->getDiscount());
				if ($customerPrices)
				{
					if ($dprice < $customerPrices)
						$customerPrices = $dprice;
				}
				else
					$customerPrices = $dprice;
			}

			if (Mage::getVersion() >= 1.7)
			{
				$group_price = parent::getGroupPrice();
				if ($group_price)
					if ($customerPrices)
					{
						if ($group_price < $customerPrices)
						{
							$customerPrices = $group_price;
						}
					}
					else
						$customerPrices = $group_price;
			}

			if ($customerPrices && $tier_price != "")
			{
				if ($tier_price < $customerPrices)
					$customerPrices = $tier_price;
			}
			else if ($customerPrices == "" && $tier_price != "")
				$customerPrices = $tier_price;

			$special_price = parent::getSpecialPrice();
			if ($customerPrices && $special_price != "")
			{
				if ($special_price < $customerPrices)
					$customerPrices = $special_price;
			}
			else if ($customerPrices == "" && $special_price != "")
				$customerPrices = $special_price;
		}
		else
		{
			// remove the 1 qty entry
			if (is_array($customerPrices))
			{
				foreach ($customerPrices as $k => $v)
				{
					if ($v['price_qty'] == 1)
					{
						unset($customerPrices[$k]);
					}
				}
				array_values($customerPrices);
			}
		}

		// if we have qty 1 and no customerprice return the normal price
		if ($customerPrices == "" && $qty == 1)
			return $price;
		return $customerPrices;
	}

	//
	/*
	public function getSpecialPrice()
	{
		$customer = Mage::getSingleton('customer/session')->getCustomerId();

		if (isset($_SESSION['adminhtml_quote']) && isset($_SESSION['adminhtml_quote']['customer_id']))
		{
			$customer = $_SESSION['adminhtml_quote']['customer_id'];
		}

		$product = $this;
		$customerObj = Mage::getModel('customer/customer')->load($customer);

		if (!Mage::helper('customerprice/productprice')->isPriceAllowed('special_price', $customerObj, $product))
		{
			return null;
		}
		return parent::getSpecialPrice();
	}
	 */

    public function getFinalPrice($qty=null)
    {
        $price = $this->_getData('my_final_price');
        if ($price !== null) {
            return $price;
        }
        return $this->getPriceModel()->getFinalPrice($qty, $this);
    }

	public function getGroupPrice()
	{
		return $this->getTierPrice(1);
	}
}
