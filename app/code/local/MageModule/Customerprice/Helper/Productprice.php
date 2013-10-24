<?php
// helper class to get the data for reverse adding product price to customer (instead of customerprice to product)
class MageModule_Customerprice_Helper_Productprice
{
    /* used for display at customer */
    public function getData($customerId)
    {
        // original
        // array
        // (
        //     '0' => array
        //     (
        //         'price_id' => '2'
        //         'website_id' => '0'
        //         'customer' => '14'
        //         'price_qty' => '11.0000'
        //         'price' => '12.0000'
        //         'website_price' => '12.0000'
        //     )
        // )
        // modification: add product_id too
        // also add value_id so that i know where to insert it later
        $customerId = (int) $customerId;
		$prefix = Mage::getConfig()->getTablePrefix();
        $sql = 'SELECT value_id, entity_id as product_id, customer_id as customer, qty as price_qty, value as price, value as website_price, website_id, `from`, `to` FROM ' . $prefix . 'catalog_product_entity_customer_price WHERE customer_id = '.$customerId;
        $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        // add readonly 
        // add productname
        foreach ($data as $k=>$v)
        {
            $v['from'] = $this->unFormatDate($v['from']);
            $v['to'] = $this->unFormatDate($v['to']);
            $data[$k] = $v;
        }
        return $data;
    }

    public function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        // unix timestamp given - simply instantiate date object
        if (preg_match('/^[0-9]+$/', $date)) {
            $date = new Zend_Date((int)$date);
        }
        // international format
        else if (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
            $zendDate = new Zend_Date();
            $date = $zendDate->setIso($date);
        }
        // parse this date in current locale, do not apply GMT offset
        else {
            $date = Mage::app()->getLocale()->date($date,
               Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
               null, false
            );
        }
        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    public function unFormatDate($date)
    {
        if (!$date)
            return null;
        $format = Mage::app()->getLocale()->getDateFormat( Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $return = Mage::app()->getLocale()->date($date, Varien_Date::DATE_INTERNAL_FORMAT)->toString($format);
        return $return;
    }

    public function getIdByCustomerId($customer_id, $websiteId = 0)
    {
        $attributeCode = 'customer_id';
        $attributeValues = array($customer_id);

        // if we have a customerid template there are multiple customer ids possible
        // - the number
        // - the number together with a template
        // first we need to determine wether we have the number or the number+template right now
        $template = Mage::getStoreConfig('customer/customer_id/id_template');
        $p = sscanf($customer_id, $template);
        if (!$p[0])
        {
            // it might be just the number or an old template..
            if (preg_match('/[1-9]+[0-9]*/', $customer_id, $result)) {
                $number = $result[0];
                $attributeValues[] = $number; // add number
                $attributeValues[] = sprintf($template, $number); // add number with new template
            }
        }
        else
        {
            // we got the template - add the number to the search list
            $number = $p[0];
            $attributeValues[] = $p[0];
        }

        foreach ($attributeValues as $k=>$v) {
            $attributeValues[$k] = addslashes($v);
        }
        $entityTypeId = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode($entityTypeId, $attributeCode);
        $attributeId = $attribute->getId();

        if (!$attributeId) {
             Mage::throwException('Attribute '.$attributeCode.' doesn\'t exist');
        }
        $sql = 'SELECT c.entity_id, v.value FROM customer_entity AS c JOIN customer_entity_varchar AS v WHERE
               v.attribute_id='.$attributeId.' AND v.value IN (\''.implode("','", $attributeValues).'\') and c.entity_id=v.entity_id';

        if ($websiteId) {
            $sql.=' AND website_id='.(int)$websiteId;
        }
        $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        $id = (isset($data[0]))?$data[0]['entity_id']:null;
        return $id;
    }

	public function getConfValue($priceType = null)
	{
		$rval = $this->_getConfValue($priceType);
		return $rval;
	}

    protected function _getConfValue($priceType=null)
    {
        $priceTypeToConfigValue = array(
            'customer_price' => 0,
            'tier_price' => 1,
            'special_price' => 2,
            'customer_discount'=> 3,
        );

		// add group price for 1.7 and up
		if (Mage::getVersion() >= 1.7)
		{
			$priceTypeToConfigValue += array('group_price' => 4);
		}

        if ($priceType === null) {
            return $priceTypeToConfigValue;
        }
        if (!isset($priceTypeToConfigValue[$priceType])) {
            throw new Exception(Mage::helper('dataflow')->__('Unknown pricetype'));
        }
        return $priceTypeToConfigValue[$priceType];
    }

    protected function _getPriceType($confValue=null)
    {
        $priceTypeToConfigValue = $this->_getConfValue();
        $configValueToPriceType = array();
        foreach ($priceTypeToConfigValue as $k=>$v)
        {
            $configValueToPriceType[$v] = $k;
        }
        if ($confValue === null) {
            return $configValueToPriceType;
        }
        if (!isset($configValueToPriceType[$confValue])) {
            throw new Exception(Mage::helper('dataflow')->__('Unknown confValue'));
        }
        return $configValueToPriceType[$confValue];
    }

    public function isPriceEnabled($priceType, $customer, $product, $qty = null)
    {


		/*
		$entityTypeId = $this->getEntityTypeId('customer');

		$attributeId = $this->getAttribute($entityTypeId, 'discount', 'attribute_id');

		$installer->updateAttribute($entityTypeId, $idAttributeOldSelect, array(
			'label' => 'Customer Price - Discount (%)'
		));

		$attributeId = $this->getAttribute($entityTypeId, 'customer_verify', 'attribute_id');

		$installer->updateAttribute($entityTypeId, $idAttributeOldSelect, array(
			'label' => 'Customer Price - Verified'
		));
		 */

		$confValue = $this->_getConfValue($priceType);

		// this is a hack since sometimes getDiscountable doesnt work for whatever reason
		if ($product)
		{
			$product = Mage::getModel('catalog/product')->load($product->getId());
		}

		// check if product is discountable
		if ($product && $product->getDiscountable() === "1")
		{
			// check if user is verified for discounts
			if ($customer && $customer->getCustomerVerify() === "1")
			{
				// check for product discountable prices
				$product_prices = Mage::getStoreConfig('customer_price/product/discountable_prices');
				if ($product_prices != '')
				{
					if (!in_array($confValue, explode(',', $product_prices)))
						return false;
				}

				// check for customer discountable prices
				$customer_prices = Mage::getStoreConfig('customer_price/customer/discountable_prices');
				if ($customer_prices != '')
				{
					if (!in_array($confValue, explode(',', $customer_prices)))
						return false;
				}
				return true;
			}
		}

		return false;
	}

    /*
        this function checks if the price is allowed for this customer
        on one side with attributes discountable from product and customer_verify from customer
        on the other side, if the price_position is enabled it will look which price is the first in order
     */
    public function isPriceAllowed($priceType, $customer, $product, $qty = null)
    {
		$confValue = $this->_getConfValue($priceType);

		if (!$this->isPriceEnabled($priceType, $customer, $product, $qty))
			return false;

		if (!$product)
			return false;

		if (Mage::getStoreConfig('customer_price/price_sorting_order/enable_sort_prices'))
		{
			// fully reload the product
			$product = $product->load($product->getId());

			$prices = Mage::Helper('customerprice/productprice')->getConfValue(null);

			$first = true;

			// check wich price is the first one that applies
			for ($i=1; $i<5; $i++)
			{
				$found = false;
				$fprice = null;
				$price_id = (int)Mage::getStoreConfig('customer_price/price_sorting_order/sort_price_' . $i);

				switch ($this->_getPriceType($price_id))
				{
					case 'special_price':
						if ($product->getData('special_price'))
							$found = true;
						break;
					case 'customer_price':
						$cprices = $product->getData('customer_price');
						if ($cprices && $this->anyCustomerPricesApplies($cprices, $customer, $qty))
							$found = true;
						break;
					case 'tier_price':
						if ($product->getData('tier_price'))
							$found = true;
						break;
					case 'customer_discount':
						if ($customer->getDiscountable() && $product->getDiscountable() === "1")
							$found = true;
						break;
				}

				if ($price_id == $confValue)
				{
					return $first;
				}

				if ($found)
					$first = false;
			}
		}
		return true;
	}

	public function anyCustomerPricesApplies($cprices, $customer, $qty)
	{
		if (is_null($qty))
			$qty = 1;

		foreach ($cprices as $cprice)
		{
			if ($cprice['customer'] == $customer)
			{
				if ($cprice['price_qty'] < $qty)
				{
					if (Mage::app()->getLocale()->isStoreDateInInterval(null, $this->formatDate($cprice['from']), $this->formDate($cprice['to'])))
					{
						return true;
					}
				}
			}
		}
		return false;
	}

    public function customerPriceApplies($cprice, $customer, $qty, $price)
    {
        if ($cprice['customer'] != $customer) {
            return false;
        }
        if (!is_null($qty) && $cprice['price_qty'] > $qty) {
            return false;
        }
        if (!is_null($price) && $cprice['website_price'] > $price) {
            return false;
        }
        $store = null;
        if (!Mage::app()->getLocale()->isStoreDateInInterval($store, $this->formatDate($cprice['from']), $this->formatDate($cprice['to']))) {
            return false;
        }
        return true;
    }

	// returns only date and customer valid customerprices
	public function customerPricesGetValid($cprices, $customer)
	{
		if ($cprices == null)
			return array();

		$valid_prices = array();

		foreach ($cprices as $cprice)
		{

			if ($cprice['customer'] != $customer)
				continue;

			$store = null;
			if (!Mage::app()->getLocale()->isStoreDateInInterval($store, $this->formatDate($cprice['from']), $this->formatDate($cprice['to'])))
			{
				continue;
			}

			array_push($valid_prices, $cprice);
		}

		return $valid_prices;
	}

	// will only return prices valid for the qty
	public function customerPriceGetQuantity($cprices, $qty)
	{
		if ($cprices == null)
			return null;

		if (is_null($qty))
			return $cprices;

		$return_price = null;

		foreach ($cprices as $cprice)
		{
			if ($cprice['price_qty'] <= $qty)
			{
				if ($return_price)
				{
					if ($return_price['website_price'] > $cprice['website_price'])
						$return_price = $cprice;
				}
				else
				{
					$return_price = $cprice;
				}
			}
		}

		return $return_price;
	}

	public function customerPriceGetCustomerId()
	{
		if (isset($_SESSION['adminhtml_quote']) && isset($_SESSION['adminhtml_quote']['customer_id'])) {
			$customer = $_SESSION['adminhtml_quote']['customer_id'];
		}
		else
		{
			$customer = Mage::getSingleton('customer/session')->getCustomerId();
		}

		return $customer;
	}

}
