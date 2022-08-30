<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Controller\Adminhtml\Storelocator;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Storelocator Factory
     *
     * @var \Drc\Storepickup\Model\StorelocatorFactory
     */
    protected $storelocatorFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->jsonFactory         = $jsonFactory;
        $this->storelocatorFactory = $storelocatorFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $storelocatorId) {
            /** @var \Drc\Storepickup\Model\Storelocator $storelocator */
            $storelocator = $this->storelocatorFactory->create()->load($storelocatorId);
            try {
                $storelocatorData = $postItems[$storelocatorId];//todo: handle dates
                $storelocator->addData($storelocatorData);
                $storelocator->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithStorelocatorId($storelocator, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithStorelocatorId($storelocator, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithStorelocatorId(
                    $storelocator,
                    __('Something went wrong while saving the Storelocator.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Storelocator id to error message
     *
     * @param \Drc\Storepickup\Model\Storelocator $storelocator
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithStorelocatorId(\Drc\Storepickup\Model\Storelocator $storelocator, $errorText)
    {
        return '[Storelocator ID: ' . $storelocator->getId() . '] ' . $errorText;
    }
}
