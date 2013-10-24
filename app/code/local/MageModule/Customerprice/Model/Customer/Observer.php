<?php
 class MageModule_Customerprice_Model_Customer_Observer extends Mage_Core_Model_Abstract
 {
    /**
     * Called in adminhtml and frontend area
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function customerSaveBefore($observer)
    {
        $customer = $observer->getCustomer();

        if (Mage::app()->getStore()->isAdmin())
        {
            $this->_setCustomerPrices($customer);
        }
    }

    /**
     * Set the customer prices for the customer model.
     * TODO could be merged somehow with Model/Catalog/Product/Attribute/Backend/Customerprice.php
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return null
     */
    protected function _setCustomerPrices(Mage_Customer_Model_Customer $customer)
    {
        $data = Mage::app()->getRequest()->getParam('account');
        if (!$data || !isset($data['customer_price']))
            return;
        $data = $data['customer_price'];
        // '0' => array
        // (
        //     'website_id' => '0'
        //     'productname' => 'asc_8'
        //     'product_id' => '30'
        //     'customer' => '15'
        //     'price_qty' => '12'
        //     'price' => '123'
        //     'delete' => ''
        // )

        // then look if there are doubled values in form of entity_id-customer_id-qty
        $unique = array();
        $notUnique = array();
        foreach ($data as $key=>$row)
        {
            if ($row['delete'])
                continue;
            $str = $row['product_id'].'-'.$row['customer'].'-'.$row['price_qty'].'-'.$row['from'].'-'.$row['to'];
            if (isset($unique[$str]))
                $notUnique[] = array($unique[$str], $key);
            else
                $unique[$str] = $key;
        }
        if (!empty($notUnique))
        {
            // $customer->_dataSaveAllowed = false;
            Mage::throwException(
                Mage::helper('catalog')->__('Duplicate website customer price customer, quantity, from and to.')
            );
            return;
        }

        // then remove all with delete=>1
        foreach ($data as $key=>$row)
        {
            if ($row['delete'])
            {
                 unset($data[$key]);
                 $bind['value_id'] = $row['value_id'];
                 Mage::getSingleton('core/resource')->getConnection('write')->delete('catalog_product_entity_customer_price',
                     Mage::getSingleton('core/resource')->getConnection()->quoteInto('value_id = ?', $row['value_id']));
             }
        }

        // then update or insert all
        foreach ($data as $key=>$row)
        {
            unset($data[$key]);
            // $sql = 'INSERT INTO catalog_product_entity_customer_price
            //         (entity_id, customer_id, qty, value, website_id)
            // VALUES ('.((int)$row['product_id']).', '.((int)$row['customer_id']).', '.((int)$row['price_qty']).',
            //     '.((double)$row['price']).', '.((int)$row['website_id']).')';
            $bind = $this->_getBind($row);
            if (!$bind['entity_id'])
                continue;
            $bind['from'] = Mage::helper('customerprice/productprice')->formatDate($bind['from']);
            $bind['to'] = Mage::helper('customerprice/productprice')->formatDate($bind['to']);    
            if ($bind['value_id'])
            {
                Mage::getSingleton('core/resource')->getConnection('write')->update('catalog_product_entity_customer_price', $bind,
                Mage::getSingleton('core/resource')->getConnection('write')->quoteInto('value_id = ?', $row['value_id']));
            }
            else
            {
                Mage::getSingleton('core/resource')->getConnection('write')->insert('catalog_product_entity_customer_price', $bind);
            }
        }
    }

    // maps the formkeys to the db columns
    protected function _getBind($row)
    {
        $bind = array();
        $bind['entity_id'] = (int)$row['product_id'];
        $bind['customer_id'] = (int)$row['customer'];
        $bind['qty'] = (int)$row['price_qty'];
        $bind['value'] = (float)$row['price'];
        $bind['website_id'] = (int)$row['website_id'];
        $bind['value_id'] = (int)$row['value_id'];
        $bind['from'] = $row['from'];
        $bind['to'] = $row['to'];
        return $bind;
    }
}
