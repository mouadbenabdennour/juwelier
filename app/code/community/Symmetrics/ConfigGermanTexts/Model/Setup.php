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
 * @package   Symmetrics_ConfigGermanTexts
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Sergej Braznikov <sb@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Setup model
 *
 * @category  Symmetrics
 * @package   Symmetrics_ConfigGermanTexts
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Sergej Braznikov <sb@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_ConfigGermanTexts_Model_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Get config.xml data
     *
     * @return array
     */
    public function getConfigData()
    {
        $configData = Mage::getConfig()
            ->getNode('default/config_german_texts')
            ->asArray();

        return $configData;
    }

    /**
     * Get config.xml data
     *
     * @param string      $node      xml noce
     * @param string|null $childNode if set, child node of the first noce
     *
     * @return array
     */
    protected function _getConfigNode($node, $childNode = null)
    {
        $configData = $this->getConfigData();
        if ($childNode) {
            return $configData[$node][$childNode];
        } else {
            return $configData[$node];
        }
    }

    /**
     * Get pages/default from config file
     *
     * @return array
     */
    public function getConfigPages()
    {
        return $this->_getConfigNode('pages', 'default');
    }

    /**
     * Get blocks/default from config file
     *
     * @return array
     */
    public function getConfigBlocks()
    {
        return $this->_getConfigNode('blocks', 'default');
    }

    /**
     * Get emails/default from config file
     *
     * @return array
     */
    public function getConfigEmails()
    {
        return $this->_getConfigNode('emails', 'default');
    }

    /**
     * Get imprint from config file
     *
     * @return array
     */
    public function getConfigImprint()
    {
        return $this->_getConfigNode('imprint');
    }

    /**
     * Get template content
     *
     * @param string $filename template file name
     *
     * @return string
     */
    public function getTemplateContent($filename)
    {
        return file_get_contents(Mage::getBaseDir() . DS . $filename);
    }

    /**
     * Get footer_links/default from config file
     *
     * @return array
     */
    public function getFooterLinks()
    {
        return $this->_getConfigNode('footer_links', 'default');
    }

    /**
     * Collect data and create CMS page
     *
     * @param array  $pageData cms page data
     * @param string $isActive if '1' cms page will be set to active
     *
     * @return void
     */
    public function createCmsPage($pageData, $isActive='1')
    {
        if (!is_array($pageData)) {
            return null;
        }

        $pageData = array(
            'title' => $pageData['title'],
            'identifier' => $pageData['identifier'],
            'content' => $this->getTemplateContent($pageData['text']),
            'root_template' => $pageData['root_template'],
            'stores' => array('0'),
            'is_active' => $isActive,
        );

        $model = Mage::getModel('cms/page');
        $page = $model->load($pageData['identifier']);

        if (!(int)$page->getId()) {
            // create
            $model->setData($pageData)->save();
        } else {
            // update
            $pageData['page_id'] = $page->getId();
            $pageData['stores'] = $page->getStoreId();
            $model->setData($pageData)->setId($pageData['page_id'])->save();
        }
    }

    /**
     * Collect data and create CMS block
     *
     * @param array   $blockData cms block data
     * @param boolean $override  override cms block if it exists
     *
     * @return void
     */
    public function createCmsBlock($blockData, $override=true)
    {
        $model = Mage::getModel('cms/block');
        $block = $model->load($blockData['identifier']);
        $blockData['content'] = $this->getTemplateContent($blockData['text']);
        if (!$block->getId()) {
            $blockData['stores'] = array('0');
            $blockData['is_active'] = '1';

            $model->setData($blockData)->save();
        } else {
            if ($override) {
                $blockData['stores'] = array('0');
                $blockData['is_active'] = '1';
                $blockData['block_id'] = $block->getId();
                $model->setData($blockData)->save();
            }
        }
    }

    /**
     * Generate footer_links block from config data
     *
     * @return string
     */
    public function createFooterLinksContent()
    {
        $footerLinksHtml = '<ul>';
        $footerLinksCounter = 0;

        foreach ($this->getFooterLinks() as $data) {
            $footerLinksCounter++;
            $title = $data['title'];
            $target = $data['target'];
            $class = '';
            if ($footerLinksCounter == count($this->getFooterLinks())) {
                $class = 'last';
            }
            $footerLinksHtml .= '<li class="'.$class.'">';
            $footerLinksHtml .= '<a href="{{store url="' . $target . '"}}">' . $title . '</a></li>';
        }

        $footerLinksHtml .= '</ul>';

        return $footerLinksHtml;
    }

    /**
     * Update footer_links cms block
     *
     * @param array $blockData cms block data
     *
     * @return void
     */
    public function updateFooterLinksBlock($blockData)
    {
        $model = Mage::getModel('cms/block');
        $block = $model->load($blockData['identifier']);

        if ($block->getId()) {
            $data = array();
            $data['block_id'] = $block->getId();
            $data['identifier'] = $blockData['identifier'] . '_backup';
            $model->setData($data)->save();
        }

        $data = array(
            'title' => $blockData['title'],
            'identifier' => $blockData['identifier'],
            'content' => $this->createFooterLinksContent(),
            'stores' => array('0'),
            'is_active' => '1',
        );

        $model->setData($data)->save();
    }

    /**
     * Create transactional email template
     *
     * @param array $emailData template data
     *
     * @return voids
     */
    public function createEmail($emailData)
    {
        $model = Mage::getModel('core/email_template');
        $template = $model->loadByCode($emailData['template_code']);
        if (!$template->getId()) {
            // create
            $template = $model->setTemplateSubject($emailData['template_subject'])
                ->setTemplateCode($emailData['template_code'])
                ->setTemplateText($this->getTemplateContent($emailData['text']))
                ->setTemplateType($emailData['template_type'])
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
                ->save();
        } else {
            //update
            $template->setTemplateSubject($emailData['template_subject'])
                ->setTemplateCode($emailData['template_code'])
                ->setTemplateText($this->getTemplateContent($emailData['text']))
                ->setTemplateType($emailData['template_type'])
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
                ->save();
        }

        $this->setConfigData($emailData['config_data_path'], $template->getId());
    }
}
