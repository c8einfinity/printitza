<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Viewfile extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Shipping carrier helper
     *
     * @var \Magento\Shipping\Helper\Carrier
     */
    protected $carrierHelper;
    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem

     * @param \Designnbuy\OrderTicket\Model\OrderTicket\OrderTicketDataMapper $orderticketDataMapper
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Designnbuy\OrderTicket\Model\OrderTicket\OrderTicketDataMapper $orderticketDataMapper,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $filesystem,
            $carrierHelper,
            $orderticketDataMapper
        );
        $this->resultRawFactory = $resultRawFactory;
        $this->urlDecoder = $urlDecoder;
    }

    /**
     * Retrieve image MIME type by its extension
     *
     * @param string $extension
     * @return string
     */
    protected function _getPlainImageMimeType($extension)
    {
        $mimeTypeMap = ['gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png'];
        $contentType = 'application/octet-stream';
        if (isset($mimeTypeMap[$extension])) {
            $contentType = $mimeTypeMap[$extension];
        }
        return $contentType;
    }

    /**
     * Action for view full sized item attribute image
     *
     * @return void
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
        $fileName = null;
        $plain = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $fileName = $this->urlDecoder->decode(
                $this->getRequest()->getParam('file')
            );
        } elseif ($this->getRequest()->getParam('image')) {
            // show plain image
            $fileName = $this->urlDecoder->decode(
                $this->getRequest()->getParam('image')
            );
            $plain = true;
        } else {
            throw new NotFoundException(__('Page not found.'));
        }

        $filePath = sprintf('orderticket_item/%s', $fileName);
        if (!$this->readDirectory->isExist($filePath)) {
            throw new NotFoundException(__('Page not found.'));
        }


        if ($plain) {
            /** @var $readFile \Magento\Framework\Filesystem\File\Read */
            $readFile = $this->readDirectory->openFile($filePath);
            $contentType = $this->_getPlainImageMimeType(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)));
            $fileStat = $this->readDirectory->stat($filePath);

            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $fileStat['size'])
                ->setHeader('Last-Modified', date('r', $fileStat['mtime']));
            $resultRaw->setContents($readFile->read($fileStat['size']));
            return $resultRaw;
        } else {
            $name = pathinfo($fileName, PATHINFO_BASENAME);
            $this->_fileFactory->create(
                $name,
                ['type' => 'filename', 'value' => $this->readDirectory->getAbsolutePath($filePath)],
                DirectoryList::MEDIA
            );
        }

    }
}
