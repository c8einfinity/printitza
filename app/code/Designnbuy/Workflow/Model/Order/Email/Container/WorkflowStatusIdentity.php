<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Model\Order\Email\Container;
use Magento\Sales\Model\Order\Email\Container\Container as OrderContainer;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface as OrderIdentityInterface;

class WorkflowStatusIdentity extends OrderContainer implements OrderIdentityInterface
{
    const XML_PATH_EMAIL_COPY_METHOD = 'dnbworkflow/workflow_status/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'dnbworkflow/workflow_status/copy_to';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'dnbworkflow/workflow_status/guest_template';
    const XML_PATH_EMAIL_TEMPLATE = 'dnbworkflow/workflow_status/template';
    const XML_PATH_EMAIL_IDENTITY = 'dnbworkflow/workflow_status/identity';
    const XML_PATH_EMAIL_ENABLED = 'dnbworkflow/workflow_status/enabled';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
