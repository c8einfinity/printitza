<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Controller\Adminhtml\Holidays;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Holidays Factory
     *
     * @var \Drc\Storepickup\Model\HolidaysFactory
     */
    protected $holidaysFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Drc\Storepickup\Model\HolidaysFactory $holidaysFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Drc\Storepickup\Model\HolidaysFactory $holidaysFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->jsonFactory         = $jsonFactory;
        $this->holidaysFactory = $holidaysFactory;
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
        foreach (array_keys($postItems) as $holidayId) {
            /** @var \Drc\Storepickup\Model\Holidays $holidays */
            $holidays = $this->holidaysFactory->create()->load($holidayId);
            try {
                $holidaysData = $postItems[$holidayId];//todo: handle dates
                $holidays->addData($holidaysData);
                $holidays->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithHolidaysId($holidays, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithHolidaysId($holidays, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithHolidaysId(
                    $holidays,
                    __('Something went wrong while saving the Holidays.')
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
     * Add Holidays id to error message
     *
     * @param \Drc\Storepickup\Model\Holidays $holidays
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithHolidaysId(\Drc\Storepickup\Model\Holidays $holidays, $errorText)
    {
        return '[Holidays ID: ' . $holidays->getId() . '] ' . $errorText;
    }
}
