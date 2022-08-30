<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Controller\Adminhtml;

abstract class Request extends \Magento\Backend\App\Action
{

    protected $coreRegistry;

    protected $requestRepository;

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->coreRegistry      = $coreRegistry;
        $this->requestRepository = $requestRepository;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
}
