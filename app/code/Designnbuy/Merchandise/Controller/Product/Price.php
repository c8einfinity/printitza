<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Merchandise\Controller\Product;

use Magento\Framework\Controller\ResultFactory;

/**
 * Product Service
 */
class Price extends \Designnbuy\Background\App\Action\Action
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog category factory
     *
     * @var \Designnbuy\Merchandise\Model\MerchandiseFactory
     */
    protected $_merchandise;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    protected $dnbBaseHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Merchandise\Model\Merchandise $merchandise,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_merchandise = $merchandise;
        $this->dnbBaseHelper = $dnbBaseHelper;
    }
    /**
     * category action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
       /* $data = '{"productId":"2057","qty":5,"colorId":"50","comment":"","printingMethod"
                :{"printableColorType":"1","printingMethodId":"2","pricingLogic":"Fixed","pricingLogicId":"1","totalColors"
                :{"Front":0,"Back":0},"isCustomized":{"Front":false,"Back":false,"Left":false,"Right":false},"totalImagesUsed"
                :{"Front":0,"Back":0,"Left":0,"Right":0}}}';*/
        $request = $this->getRequest()->getParams();
        try {
            $result = $this->dnbBaseHelper->getProductPrice($request);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
