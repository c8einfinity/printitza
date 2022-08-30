<?php

namespace Designnbuy\Designidea\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Abstract Action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    const PARAM_ID = 'id';

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Banner factory.
     *
     * @var \Designnbuy\Designidea\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * Designidea factory.
     *
     * @var \Designnbuy\Designidea\Model\DesignideaFactory
     */
    protected $_designideaFactory;

    /**
     * Designidea Category factory.
     *
     * @var \Designnbuy\Designidea\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Banner Collection Factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * Designidea Collection Factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * Designidea Category Collection Factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * File Factory.
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    protected $dnbBaseHelper;


    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Backend\App\Action\Context                                        $context
     * @param \Magento\Framework\Registry                                                $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory                           $fileFactory
     * @param \Magento\Framework\View\Result\PageFactory                                 $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                               $resultLayoutFactory
     * @param \Magento\Framework\View\LayoutFactory                                         $layoutFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory                              $resultJsonFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory                          $resultForwardFactory
     * @param \Magento\Backend\Helper\Js                                                 $jsHelper
     * @param \Designnbuy\Designidea\Model\BannerFactory                           $bannerFactory
     * @param \Designnbuy\Designidea\Model\DesignideaFactory                           $designideaFactory
     * @param \Designnbuy\Designidea\Model\ResourceModel\Banner\CollectionFactory  $bannerCollectionFactory
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory  $designideaCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\Helper\Js $jsHelper,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Designnbuy\Designidea\Model\CategoryFactory $categoryFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        $rootDirBasePath = DirectoryList::MEDIA
    ) {
        parent::__construct($context);

        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_jsHelper = $jsHelper;
        $this->_designideaFactory = $designideaFactory;
        $this->_designideaCollectionFactory = $designideaCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_outputHelper = $outputHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->jsonHelper = $jsonHelper;
        $this->rootDirBasePath = $rootDirBasePath;
    }

    /**
     * Get result redirect after add/edit action
     *
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirect
     * @param null                                          $paramCrudId
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _getResultRedirect(\Magento\Framework\Controller\Result\Redirect $resultRedirect, $paramId = null)
    {
        $back = $this->getRequest()->getParam('back');

        switch ($back) {
            case 'new':
                $resultRedirect->setPath('*/*/new', ['_current' => true]);
                break;
            case 'edit':
                $resultRedirect->setPath('*/*/edit', ['id' => $paramId, '_current' => true]);
                break;
            default:
                $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
