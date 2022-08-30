<?php
namespace Designnbuy\Notifications\Block\Adminhtml;

/**
 * Admin Notifications
 */
class NotificationsMessage extends \Magento\Framework\View\Element\Template
{
	const TYPE_DESIGNER = 1;
    const TYPE_RESELLER = 2;
    const TYPE_DESIGN = 3;
    const TYPE_REPORT = 4;
    const TYPE_REDEEM = 5;
    const TYPE_UNPUBLISED = 6;
    const UNREAD_MESSAGE = 0;

 	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Notifications\Model\ResourceModel\Notifications\CollectionFactory $collectionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
        $this->_pricingHelper = $pricingHelper;
        $this->request = $request;
    }

    public function getNotificationReportContent() {
    	$_message = array();
    	
    	$_notificationCollection = $this->_collectionFactory->create();
    	$unReadMessage = $_notificationCollection->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$_message['unread_message_count'] = count($unReadMessage);

    	$_notificatoinDesigner = $this->_collectionFactory->create();
        $designerMessage = $_notificatoinDesigner->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$designerMessage = $_notificatoinDesigner->addFieldToFilter('type', self::TYPE_DESIGNER);
    	$_message['designer_count'] = count($designerMessage);
    	
    	$_notificatoinShop = $this->_collectionFactory->create();
        $designerMessage = $_notificatoinShop->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$designerMessage = $_notificatoinShop->addFieldToFilter('type', self::TYPE_RESELLER);
    	$_message['shop_count'] = count($designerMessage);

    	$_notificatoinDesign = $this->_collectionFactory->create();
        $designMessage = $_notificatoinDesign->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$designMessage = $_notificatoinDesign->addFieldToFilter('type', self::TYPE_DESIGN);
    	$_message['design_count'] = count($designMessage);

    	$_notificatoinReport = $this->_collectionFactory->create();
        $reportMessage = $_notificatoinReport->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$reportMessage = $_notificatoinReport->addFieldToFilter('type', self::TYPE_REPORT);
    	$_message['report_count'] = count($reportMessage);

    	$_notificatoinRedeem = $this->_collectionFactory->create();
        $redeemMessage = $_notificatoinDesign->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$redeemMessage = $_notificatoinRedeem->addFieldToFilter('type', self::TYPE_REDEEM);
    	$_message['redeem_count'] = count($redeemMessage);

    	$_notificatoinUnpublisedDesign = $this->_collectionFactory->create();
        $unpublishedMessage = $_notificatoinUnpublisedDesign->addFieldToFilter('is_read', self::UNREAD_MESSAGE);
    	$unpublishedMessage = $_notificatoinUnpublisedDesign->addFieldToFilter('type', self::TYPE_UNPUBLISED);
    	$_message['unpublished_design_count'] = count($unpublishedMessage);
    	return $_message;
    }

    public function getFullPageAction(){
    	return $this->request->getFullActionName();
    }
}
