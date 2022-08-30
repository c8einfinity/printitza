<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Template\Controller\Template;

use Magento\Framework\Controller\ResultFactory;
use DOMDocument;

/**
 * Template Template view
 */
class Templatedata extends \Designnbuy\Template\App\Action\Action
{
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Base\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    /**
     * View Template template action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        ini_set('display_errors', 1);

        $request = $this->getRequest()->getParam('id');
        $templates = explode(',',$request);
        $allTemplateData = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseurl = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue("web/unsecure/base_url");
        
        foreach($templates as $temp) {
            $template = $objectManager->create('Designnbuy\Template\Model\Template')->load($temp);
            $templateSvg = array();
            $templateSvg = explode(",",$template->getSvg());
            $tempSvg = array();
            
            foreach($templateSvg as $svg){
                
                    $tempSvg['svgdata'][$svg] = file_get_contents($this->helper->getTemplateSVGDir().$svg);
            
            }

            $template->setData("website_ids" , array(0 => 0, 1 => 1));
            $allTemplateData[$temp] = array_merge($template->getData(),$tempSvg);
        }
        
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($allTemplateData);
    }
}
