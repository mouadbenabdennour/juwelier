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
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * This is info renderer for Symmetrics_TweaksGerman system config.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Block_Adminhtml_System_Config_Info
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset //Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Constructor — sets the template.
     * 
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('tweaksgerman/system/config/info.phtml');
    }
    
    /**
     * Render to return the html-content of sub-menu,
     * under Admin Panel / System / Configuration.
     *
     * @param Varien_Data_Form_Element_Abstract $element element
     * 
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $this->setElement($element);
        
        $html .= '<tr><td>' . $this->toHtml() . '</tr></td>';
        $html .= $this->_getFooterHtml($element);
        
        return $html;
    }
}
