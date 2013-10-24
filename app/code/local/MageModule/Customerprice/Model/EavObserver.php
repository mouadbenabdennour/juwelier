<?php

class MageModule_Customerprice_Model_EavObserver
{
    /* fixes following error:
        when you give a product 2 customerprices for one customer
        log in with that customer and search for this product
        there will be some problem with loading twice the same product
        -> fix: say in sql we just need one product
    */
    public function load_eav($observer)
    {
        $collection = $observer->getCollection();
        $collection->getSelect()->group('e.entity_id');
    }
}
