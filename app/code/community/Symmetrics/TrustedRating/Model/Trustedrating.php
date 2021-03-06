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
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Trusted rating main class.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TrustedRating_Model_Trustedrating extends Mage_Core_Model_Abstract
{
    /**
     * @const WIDGET_LINK Fixed part of the link for the rating-site for the widget.
     */
    const WIDGET_LINK = 'https://www.trustedshops.com/bewertung/widget/widgets/';

    /**
     * @const EMAIL_WIDGET_LINK Fixed part of the link for the rating-site for the email - widget.
     */
    const EMAIL_WIDGET_LINK = 'https://www.trustedshops.com/bewertung/widget/img/';

    /**
     * @const REGISTRATION_LINK_PREFIX Fixed part of the registration link,
     *                                  prepended to all localized variations below.
     */
    const REGISTRATION_LINK_PREFIX = 'https://www.trustedshops.com/';

    /**
     * @const REGISTRATION_LINK_DE Fixed part of the registration link, German variant.
     */
    const REGISTRATION_LINK_DE = 'bewertung/anmeldung.html?';

    /**
     * @const REGISTRATION_LINK_EN Fixed part of the registration link, English variant.
     */
    const REGISTRATION_LINK_EN = 'buyerrating/signup.html';

    /**
     * @const REGISTRATION_LINK_FR Fixed part of the registration link, French variant.
     */
    const REGISTRATION_LINK_FR = 'evaluation/inscription.html?';

    /**
     * @const REGISTRATION_LINK_PL Fixed part of the registration link, Polish variant.
     */
    const REGISTRATION_LINK_PL = 'opinia/ocen_TSID.html?';

    /**
     * @const IMAGE_LOCAL_PATH Fixed part of the widget path.
     */
    const IMAGE_LOCAL_PATH = 'media/';

    /**
     * @const CACHEID The cacheid to cache the widget.
     */
    const CACHEID = 'trustedratingimage';

    /**
     * @ const EMAIL_CACHEID The cacheid to cache the email widget.
     */
    const EMAIL_CACHEID = 'trustedratingemailimage';

    /**
     * @ const CONFIG_DAYS_INTERVAL System configuration path to day interval setting.
     */
    const CONFIG_DAYS_INTERVAL = 'trustedrating/trustedrating_email/days';

    /**
     * @ const CONFIG_LANGUAGE System configuration path to selected language.
     */
    const CONFIG_LANGUAGE = 'trustedrating/data/trustedrating_ratinglanguage';

    /**
     * @ const MYSQL_DATE_FORMAT Date format for MySQL comparison (ZEND_DATE).
     */
    const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @ const WIDGET_FILE_SUFFIX File suffix for trusted rating widget.
     */
    const WIDGET_FILE_SUFFIX = '.gif';

    /**
     * Get the Trusted Shops ID from system configugration.
     * 
     * @param mixed $storeId ID of Store.
     *
     * @return string
     */
    public function getTsId($storeId = null)
    {
        return Mage::helper('trustedrating')->getTsId($storeId);
    }

    /**
     * Get the module status from system configugration.
     *
     * @return string
     */
    public function getIsActive()
    {
        return Mage::helper('trustedrating')->getIsActive();
    }

    /**
     * Get the selected language (for the rating - site) from the store config and returns
     * the link for the widget, which is stored in the module config for each language.
     *
     * @param string $type    type
     * @param int    $storeId store id
     *
     * @return string
     */
    public function getRatingLinkData($type, $storeId = null)
    {
        $optionValue = Mage::getStoreConfig(self::CONFIG_LANGUAGE, $storeId);
        $link = Mage::helper('trustedrating')->getConfig($type, $optionValue);

        return $link;
    }

    /**
     * Check if the current language is chosen in the trusted rating config.
     *
     * @return boolean
     */
    public function checkLocaleData()
    {
        $storeId = Mage::app()->getStore()->getId();
        $countrycode = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
        $countryCode = substr($countrycode, 0, 2);

        if (Mage::getStoreConfig(self::CONFIG_LANGUAGE) == $countryCode) {
            return true;
        }

        return false;
    }

    /**
     * Get the rating link.
     *
     * @return string
     */
    public function getRatingLink()
    {
        return $this->getRatingLinkData('overviewlanguagelink');
    }

    /**
     * Get the email rating link.
     *
     * @param string|int $storeId ID of Store.
     * 
     * @return string
     */
    public function getEmailRatingLink($storeId = null)
    {
        if (null == $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        
        return $this->getRatingLinkData('ratinglanguagelink', $storeId);
    }

    /**
     * Get the link data for the widget-image from cache.
     *
     * @return array
     */
    public function getRatingWidgetData()
    {
        $tsId = $this->getTsId();

        if (!Mage::app()->loadCache(self::CACHEID)) {
            $this->cacheImage($tsId);
        }

        return array(
            'tsId' => $tsId,
            'ratingLink' => $this->getRatingLink(),
            'imageLocalPath' => self::IMAGE_LOCAL_PATH
        );
    }

    /**
     * Get the link data for the email-widget-image from cache.
     *
     * @return array
     */
    public function getEmailWidgetData()
    {
        $tsId = $this->getTsId();
        $orderId = Mage::getSingleton('checkout/type_onepage')->getCheckout()->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($orderId);
        $incrementId = $order->getRealOrderId();
        $buyerEmail = $order->getData('customer_email');

        if (!Mage::app()->loadCache(self::EMAIL_CACHEID)) {
            $this->cacheEmailImage();
        }

        $array = array(
            'tsId' => $tsId,
            'ratingLink' => $this->getEmailRatingLink(),
            'imageLocalPath' => self::IMAGE_LOCAL_PATH,
            'orderId' => $incrementId,
            'buyerEmail' => $buyerEmail,
            'widgetName' => $this->getRatingLinkData('emailratingimage')
        );

        return $array;
    }

    /**
     * Cache the widget images.
     *
     * @param string $type type
     * @param string $tsId Trusted Rating Id
     *
     * @return void
     */
    private function _cacheImageData($type, $tsId = null)
    {
        $ioObject = new Varien_Io_File();
        $ioObject->open();

        if ($type == 'emailWidget') {
            $emailWidgetName = $this->getRatingLinkData('emailratingimage');
            $readPath = self::EMAIL_WIDGET_LINK . $emailWidgetName;
            $writePath = self::IMAGE_LOCAL_PATH . $emailWidgetName;
            $cacheId = self::EMAIL_CACHEID;
        } else {
            $readPath = self::WIDGET_LINK . $tsId . self::WIDGET_FILE_SUFFIX;
            $writePath = self::IMAGE_LOCAL_PATH . $tsId . self::WIDGET_FILE_SUFFIX;
            $cacheId = self::CACHEID;
        }

        $result = $ioObject->read($readPath);
        $ioObject->write($writePath, $result);
        Mage::app()->saveCache($writePath, $cacheId, array(), 1);
        $ioObject->close();
    }

    /**
     * Cache the email image.
     *
     * @return void
     */
    public function cacheEmailImage()
    {
        $this->_cacheImageData('emailWidget');
    }

    /**
     * Cache the widget image.
     *
     * @param int $tsId Trusted Rating Id
     *
     * @return void
     */
    public function cacheImage($tsId)
    {
        $this->_cacheImageData('mainWidget', $tsId);
    }

    /**
     * Get language dependent static part of URL.
     *
     * @return string|bool
     */
    public function getRegistrationPrefix()
    {
        if (!$this->checkLocaleData()) {
            return false;
        }
        $prefix = REGISTRATION_LINK_PREFIX;
        $storeId = Mage::app()->getStore()->getId();
        $countrycode = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
        $countryCode = substr($countrycode, 0, 2);
        switch ($countryCode) {
            case 'de':
                return $prefix . self::REGISTRATION_LINK_DE;
                break;
            case 'en':
                return $prefix . self::REGISTRATION_LINK_EN;
                break;
            case 'fr':
                return $prefix . self::REGISTRATION_LINK_FR;
                break;
            case 'pl':
                return $prefix . self::REGISTRATION_LINK_PL;
                break;
        }
    }

    /**
     * Return registration Link
     *
     * @return string
     */
    public function getRegistrationLink()
    {
        $link = $this->getRegistrationPrefix();
        $params = array('partnerPackage' => Mage::helper('trustedrating')->getConfig('soapapi', 'partnerpackage'));

        /* if symmetrics_imprint is installed, get data from there */
        if ($data = Mage::getStoreConfig('general/imprint')) {
            $params += array(
                'company' => $data['company_first'],
                'website' => $data['web'],
                'street' => $data['street'],
                'zip' => $data['zip'],
                'city' => $data['city'],
                'buyerEmail' => $data['email'],
            );

            $link .= http_build_query($params);
        }

        return $link;
    }

    /**
     * Get all shippings which are older than x days and are not in table
     *
     * @return boolean|array
     */
    public function checkShippings()
    {
        if (!$dayInterval = $this->getDayInterval()) {
            return false;
        }

        $dateFrom = $dayInterval['from'];
        $dateTo = $dayInterval['to'];
        $trustedRatingStores = Mage::helper('trustedrating')->getAllTrustedRatingStores();

        $shipments = Mage::getResourceModel('sales/order_shipment_collection');
        /* @var $shipments Mage_Sales_Model_Resource_Order_Shipment_Collection */
        
        if ($sentIds = $this->_getSentIds()) {
            if (!is_null($sentIds)) {
                $shipments->addAttributeToFilter('entity_id', array('nin' => $sentIds));
            }
        }
        $shipments->addAttributeToFilter('created_at', array('from' => $dateFrom, 'to' => $dateTo))
            ->addAttributeToFilter('store_id', array('in' => $trustedRatingStores))
            ->load();
        
        if (!$shipments) {
            return false;
        }
        return $shipments->getAllIds();
    }

    /**
     * Get all IDs from trusted_rating table of customers which already got an email
     *
     * @return array
     */
     private function _getSentIds()
     {
         $mailModel = Mage::getModel('trustedrating/mail');
         $shipmentIds = array();
         $model = $mailModel->getCollection();
         $items = $model->getItems();
         foreach ($items as $item) {
             $shipmentIds[] = $item->getShippmentId();
         }

         return $shipmentIds;
     }

    /**
     * Substract the days in the config (3 for default) from the current date for upper limit
     * and get the "include since" date (default: setup date) for lower limit; return both in array
     *
     * @return array
     */
    public function getDayInterval()
    {
        $fromString = Mage::helper('trustedrating')->getActiveSince();
        $dayInterval = (float) Mage::getStoreConfig(self::CONFIG_DAYS_INTERVAL);
        if (is_null($dayInterval) || $dayInterval < 0) {
            return false;
        }

        // Convert days to seconds.
        $intervalSeconds = $dayInterval * 86400;
        $date = new Zend_Date();
        $timestamp = $date->get();

        $diff = $timestamp - $intervalSeconds;

        return array(
            'from' => $fromString,
            'to' => date(self::MYSQL_DATE_FORMAT, $diff)
        );
    }
}
