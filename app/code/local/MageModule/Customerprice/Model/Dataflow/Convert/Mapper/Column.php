<?php

class MageModule_Customerprice_Model_Dataflow_Convert_Mapper_Column extends Mage_Dataflow_Model_Convert_Mapper_Column
{
    /**
     * Dataflow batch model
     *
     * @var Mage_Dataflow_Model_Batch
     */
    protected $_batch;

    /**
     * Dataflow batch export model
     *
     * @var Mage_Dataflow_Model_Batch_Export
     */
    protected $_batchExport;

    /**
     * Dataflow batch import model
     *
     * @var Mage_Dataflow_Model_Batch_Import
     */
    protected $_batchImport;

    /**
     * Retrieve Batch model singleton
     *
     * @return Mage_Dataflow_Model_Batch
     */
    public function getBatchModel()
    {
        if (is_null($this->_batch)) {
            $this->_batch = Mage::getSingleton('dataflow/batch');
        }
        return $this->_batch;
    }

    /**
     * Retrieve Batch export model
     *
     * @return Mage_Dataflow_Model_Batch_Export
     */
    public function getBatchExportModel()
    {
        if (is_null($this->_batchExport)) {
            $object = Mage::getModel('dataflow/batch_export');
            $this->_batchExport = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_batchExport);
    }

    /**
     * Retrieve Batch import model
     *
     * @return Mage_Dataflow_Model_Import_Export
     */
    public function getBatchImportModel()
    {
        if (is_null($this->_batchImport)) {
            $object = Mage::getModel('dataflow/batch_import');
            $this->_batchImport = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_batchImport);
    }

    public function map()
    {
        $batchModel  = $this->getBatchModel();
        $batchExport = $this->getBatchExportModel();

        $batchExportIds = $batchExport
            ->setBatchId($this->getBatchModel()->getId())
            ->getIdCollection();

        $onlySpecified = (bool)$this->getVar('_only_specified') === true;

        if (!$onlySpecified) {
            foreach ($batchExportIds as $batchExportId) {
                $batchExport->load($batchExportId);
                $batchModel->parseFieldList($batchExport->getBatchData());
            }

            return $this;
        }

        if ($this->getVar('map') && is_array($this->getVar('map'))) {
            $attributesToSelect = $this->getVar('map');
        }
        else {
            $attributesToSelect = array();
        }

        if (!$attributesToSelect) {
            $this->getBatchExportModel()
                ->setBatchId($this->getBatchModel()->getId())
                ->deleteCollection();

            throw new Exception(Mage::helper('dataflow')->__('Error in field mapping: field list for mapping is not defined.'));
        }

        $skuToExportId = array();
        foreach ($batchExportIds as $batchExportKey => $batchExportId)
        {
            $batchExport = $this->getBatchExportModel()->load($batchExportId);
            $row = $batchExport->getBatchData();
            if (!isset($skuToExportId[$row['sku']]))
                $skuToExportId[$row['sku']] = array();
            $skuToExportId[$row['sku']][] = $batchExportId;
        }

        $usedBatchExportIds = array();
        foreach ($batchExportIds as $batchExportId) {
            if (in_array($batchExportKey, $usedBatchExportIds))
                continue;
            $batchExport = $this->getBatchExportModel()->load($batchExportId);
            $row = $batchExport->getBatchData();

            $newRow = array();
            foreach ($attributesToSelect as $field => $mapField) {
                /** Start Customer Price Change */
                if ($field == 'customer_price')
                    continue;
                /** End Customer Price Change */
                $newRow[$mapField] = isset($row[$field]) ? $row[$field] : null;
            }

            /** Start Customer Price Change */
            if (isset($attributesToSelect['customer_price']))
            {
                // will create several new rows
                $productId = Mage::getModel('catalog/product')->getIdBySku($row['sku']);
                $product = Mage::getModel('catalog/product')->load($productId);
                $newRows = $this->_mapCustomerPrice($product, $mapField, $newRow);
            }
            else
            {
                $newRows = array($newRow);
            }
            foreach ($newRows as $k=>$newRow)
            {
                $batchExportId = $skuToExportId[$row['sku']][$k];
                $usedBatchExportIds[] = $batchExportId;
                $batchExport = $this->getBatchExportModel()->load($batchExportId);
                $batchExport->setBatchData($newRow)
                    ->setStatus(2)
                    ->save();
                $this->getBatchModel()->parseFieldList($batchExport->getBatchData());
            }
            /** End Customer Price Change */
        }

        return $this;
    }

    /** Start Customer Price Change */
    protected function _mapCustomerPrice($product, $mapField, &$origRow)
    {
        $newRows = array();
        $mapField = explode(',', $mapField);
        foreach ($mapField as $k=>$v)
            $mapField[$k] = trim($v);
        $customerPrices = $product->getData('customer_price');
        // 0=customer
        // 1=qty
        // 2=price
        // 3=website
        // 4=from
        // 5=to
        if (!empty($customerPrices))
        {
            foreach ($customerPrices as $cPrice)
            {
                $newRow = $origRow;
                if (isset($mapField[0]))
                {
                    // export customerId instead of internal id
                    $customer = Mage::getModel('customer/customer')->load($cPrice['customer']);
                    if ($customer->getCustomerId())
                        $newRow[$mapField[0]] = $customer->getCustomerId();
                    else
                        $newRow[$mapField[0]] = '';
                        // $newRow[$mapField[0]] = "The customer with id ".$customer->getId()." has no customer id";
                }
                if (isset($mapField[1]))
                    $newRow[$mapField[1]] = $cPrice['price_qty'];
                if (isset($mapField[2]))
                    $newRow[$mapField[2]] = $cPrice['price'];
                if (isset($mapField[3]))
                    $newRow[$mapField[3]] = $cPrice['website_id'];
                if (isset($mapField[4]))
                    $newRow[$mapField[4]] = $cPrice['from'];
                if (isset($mapField[5]))
                    $newRow[$mapField[5]] = $cPrice['to'];
                $newRows[] = $newRow;
            }
        }
        else
        {
            $newRows[] = $origRow;
        }
        return $newRows;
    }
    /** End Customer Price Change */
}
