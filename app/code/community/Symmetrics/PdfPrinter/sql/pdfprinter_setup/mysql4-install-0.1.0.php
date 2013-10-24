<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_PdfPrinter
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
 

$installer = $this;
$installer->startSetup();

// make directory for pdf cache
try {
    $pdfPrinterCacheDir = Mage::getBaseDir('media') . DS . 'pdfprinter';
    if (!is_dir($pdfPrinterCacheDir)) {
        mkdir($pdfPrinterCacheDir);
    }
    if (!is_writable($pdfPrinterCacheDir)) {
        chmod($pdfPrinterCacheDir, 0777);
    }
} catch(Exception $e) {
    throw new Exception(
        'Directory ' . $pdfPrinterCacheDir . ' is not writable or couldn\'t be '
        . 'created. Please do it manually.' . $e->getMessage()
    );
}

$installer->endSetup();