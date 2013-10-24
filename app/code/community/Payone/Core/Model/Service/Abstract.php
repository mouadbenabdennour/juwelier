<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone_Core to newer
 * versions in the future. If you wish to customize Payone_Core for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Core_Model
 * @subpackage      Service
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Core_Model
 * @subpackage      Service
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
abstract class Payone_Core_Model_Service_Abstract
{
    /**
     * @var Payone_Core_Model_Config_Interface
     */
    protected $configStore = null;
    /** @var Payone_Core_Model_Factory */
    protected $factory = null;
    /** @var Payone_Core_Helper_Data */
    protected $helper = null;

    /**
     *
     * @return Payone_Core_Model_Factory
     */
    public function getFactory()
    {
        if ($this->factory === null) {
            $this->factory = new Payone_Core_Model_Factory();
        }
        return $this->factory;
    }

    /**
     *
     * @param Payone_Core_Model_Factory $factory
     */
    public function setFactory(Payone_Core_Model_Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return Payone_Core_Helper_Data
     */
    protected function helper()
    {
        if ($this->helper === null) {
            $this->helper = $this->getFactory()->helper();
        }
        return $this->helper;
    }

    /**
     * @param Payone_Core_Model_Config_Interface $config
     */
    public function setConfigStore(Payone_Core_Model_Config_Interface $config)
    {
        $this->configStore = $config;
    }

    /**
     * @return Payone_Core_Model_Config_Interface
     */
    public function getConfigStore()
    {
        return $this->configStore;
    }

    /**
     * @return Payone_Core_Helper_Config
     */
    protected function helperConfig()
    {
        return $this->getFactory()->helperConfig();
    }


    /**
     * @return Payone_Core_Helper_Registry
     */
    protected function helperRegistry()
    {
        return $this->getFactory()->helperRegistry();
    }
}