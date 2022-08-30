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
namespace Designnbuy\Reseller\Block\Adminhtml\Request\Edit\Buttons;

class Save extends \Designnbuy\Reseller\Block\Adminhtml\Request\Edit\Buttons\Generic implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $requestId = $this->context->getRequest()->getParam('request_id');
        if( $requestId){
            $model = $this->requestRepository->getById($requestId);
            $status = $model->getStatus();
            if($status != null)return [];
        }
        return [
            'label' => __('Approve'),
            'class' => 'save primary',
            'on_click' => sprintf("location.href = '%s';", $this->getSaveUrl()),
            'sort_order' => 90,
        ];
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/approve', ['request_id' => $this->getRequestId(),'type' => 'approve']);
    }
}
