<?php
class MageModule_Customerprice_Model_CustomerObserver extends Mage_Core_Model_Abstract
{
    /**
     * Called in adminhtml and frontend area
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function customerSaveBefore($observer)
    {
        if (!Mage::getStoreConfig('customer/customer_id/auto_increment'))
            return;

        $nextId = (int)Mage::getStoreConfig('customer/customer_id/next_increment');
        $template = Mage::getStoreConfig('customer/customer_id/id_template');

        $customer = $observer->getCustomer();

        // admin manually changes the id
        if (Mage::app()->getStore()->isAdmin())
        {
            if ($customer->getCustomerId())
            {
                sscanf($customer->getCustomerId(), $template, $id);

                if ($id >= $nextId)
                {
                    $nextId = $id+1;
                    Mage::getConfig()->saveConfig('customer/customer_id/next_increment', $nextId);
                    Mage::getConfig()->reinit();
                    Mage::app()->reinitStores();

                }
            }
        }

        // customer has no customer id yet - so give him one
        if (!$customer->getCustomerId())
        {
            $customer->setCustomerId(sprintf($template, $nextId));
            $nextId++;
            Mage::getConfig()->saveConfig('customer/customer_id/next_increment', $nextId);
            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();
        }
    }

    public function customerLoadAfter($observer)
    {
        if (!Mage::getStoreConfig('customer/customer_id/auto_increment'))
            return;
        if (!Mage::getStoreConfig('customer/customer_id/template_force_apply'))
            return;

        $customer = $observer->getCustomer();
        if (!$customer->getCustomerId())
            return;

        // no number inside customer_id
        if (!preg_match('/[1-9]+[0-9]*/', $customer->getCustomerId(), $result))
            return;
        $number = $result[0];

        $template = Mage::getStoreConfig('customer/customer_id/id_template');

        $p = sscanf($customer->getCustomerId(), $template);
        if (!$p[0])
        {
            $customer->setCustomerId(sprintf($template, $number));
        }
    }
}
