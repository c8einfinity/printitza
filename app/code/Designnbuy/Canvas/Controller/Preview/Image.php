<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Canvas\Controller\Preview;

use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * SaveStringOnServer Action
 */
class Image extends \Magento\Framework\App\Action\Action
{
    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Customer Helper Class
     *
     * @var \Designnbuy\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */

    private $dnbBaseHelper;

    /**
     * Canvas
     *
     * @var \Designnbuy\Canvas\Model\Canvas
     */
    protected $_canvas;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Designnbuy\Customer\Helper\Data $customerHelper,
        \Designnbuy\Canvas\Model\Canvas $canvas,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->_outputHelper = $outputHelper;
        $this->jsonHelper = $jsonHelper;
        $this->_customerHelper = $customerHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->_canvas = $canvas;
        $this->resultRawFactory = $resultRawFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }
    /**
     * SaveStringOnServer action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $request['id'] = $productId = $params['id'];
        $request['customerDetails'] = $customerDetails = $this->_customerHelper->getCustomerDetails();

        $_product = $this->productRepository->getById($productId, false);
        $customCanvasAttributeSetId = $this->dnbBaseHelper->getCustomCanvasAttributeSetId();

        $isQuickEditEnable = $this->_canvas->isQuickEditEnable($_product);
        $templateId = $_product->getTemplateId();
        $template = $this->_canvas->getTemplateDesign($templateId);

        $quickEditSvg = array();
        if (isset($template) && !empty($template)) {
            if (isset($template['svg']) && $template['svg'] != '') {
                $quickEditSvg = array($template['svg'][0]);
            } else {
                $isQuickEditEnable = false;
            }
        }
        $request['svg'] =  $quickEditSvg;

        $output = $this->_outputHelper->generateCustomerPreviewFromDesign($request);

        $stat = $this->_mediaDirectory->stat($output);
        $contentLength = $stat['size'];
        $contentModify = $stat['mtime'];
        $contentType = 'image/png';
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Last-Modified', date('r', $contentModify));
        $resultRaw->setContents($this->_mediaDirectory->readFile($output));
        return $resultRaw;
    }

}
