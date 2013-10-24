<?php
class MageModule_Customerprice_Model_CustomerEntity extends Mage_Customer_Model_Entity_Customer
{
    /**
     * Load customer by customer_id
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $customer_id
     * @param bool $testOnly
     * @return Mage_Customer_Model_Entity_Customer
     * @throws Mage_Core_Exception
     */
    public function loadByCustomerId(Mage_Customer_Model_Customer $customer, $customer_id, $testOnly = false)
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

        foreach ($attributeValues as $k=>$v)
            $attributeValues[$k] = addslashes($v);

        $entityTypeId = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode($entityTypeId, $attributeCode);

        $attributeId = $attribute->getId();

        if (!$attributeId)
             Mage::throwException('Attribute '.$attributeCode.' doesn\'t exist');

        $sql = 'SELECT c.entity_id, v.value FROM customer_entity AS c JOIN customer_entity_varchar AS v WHERE
            v.attribute_id='.$attributeId.' AND v.value IN (\''.implode("','", $attributeValues).'\') and c.entity_id=v.entity_id';

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(Mage::helper('customer')->__('Customer website ID must be specified when using the website scope.'));
            }
            $sql.=' AND website_id='.(int)$customer->getWebsiteId();
        }

        $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        $id = (isset($data[0]))?$data[0]['entity_id']:null;

        if ($id)
            $this->load($customer, $id);
        else
            $customer->setData(array());
        return $this;
    }
}
