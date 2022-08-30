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
namespace Designnbuy\Reseller\Controller\Adminhtml\Request;

abstract class MassAction extends \Magento\Backend\App\Action
{
    /**
     * Request repository
     * 
     * @var \Designnbuy\Reseller\Api\RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * Mass Action filter
     * 
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * Request collection factory
     * 
     * @var \Designnbuy\Reseller\Model\ResourceModel\Request\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Action success message
     * 
     * @var string
     */
    protected $successMessage;

    /**
     * Action error message
     * 
     * @var string
     */
    protected $errorMessage;

    /**
     * constructor
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Designnbuy\Reseller\Model\ResourceModel\Request\CollectionFactory $collectionFactory
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Designnbuy\Reseller\Model\ResourceModel\Request\CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->requestRepository = $requestRepository;
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->successMessage    = $successMessage;
        $this->errorMessage      = $errorMessage;
        parent::__construct($context);
    }

    /**
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @return mixed
     */
    abstract protected function massAction(\Designnbuy\Reseller\Api\Data\RequestInterface $request);

    /**
     * execute action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $request) {
                $this->massAction($request);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->errorMessage);
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('designnbuy_reseller/*/index');
        return $redirectResult;
    }
}
