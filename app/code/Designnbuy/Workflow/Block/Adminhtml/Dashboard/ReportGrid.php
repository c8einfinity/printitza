<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Block\Adminhtml\Dashboard;

/**
 * Adminhtml dashboard bottom tabs
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ReportGrid extends \Magento\Backend\Block\Dashboard\Grids
{
    protected $aclRetriever;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        array $data = []
    ) {

        $this->aclRetriever = $aclRetriever;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_authSession = $authSession;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }



    protected function _prepareLayout()
    {

        // load this active tab statically        
        parent::_prepareLayout();
        if($this->isSuperAdmin()){
            // Add workflow status report tab @13
            $this->addTab(
                'workflow_status_report',
                [
                    'label' => __('Workflow Report'),
                    'content' => $this->getLayout()->createBlock(
                        \Designnbuy\Workflow\Block\Adminhtml\Report::class
                    )->toHtml(),
                    'active' => true
                ]
            );
        }


        //return parent::_prepareLayout();
    }

    public function isSuperAdmin()
    {
        $role = $this->getCurrentUser()->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());

        $resource = 'Magento_Backend::all';
        return in_array($resource, $resources);

    }

    public function getCurrentUser()
    {
        return $this->_authSession->getUser();
    }
}
