<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Template\Controller\Layout;

use Magento\Framework\Controller\ResultFactory;
/**
 * Template Template view
 */
class LayoutList extends \Designnbuy\Template\App\Action\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Template Helper
     *
     * @var \Designnbuy\Template\Helper\Data
     */
    protected $_templateHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
       // \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Template\Helper\Data $templateHelper
    ) {
        parent::__construct($context);
        //$this->_storeManager = $storeManager;
        $this->_templateHelper = $templateHelper;
    }

    /**
     * View Template template action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        try {
            $result = $this->_templateHelper->getProductRelatedLayouts($request);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }


}
