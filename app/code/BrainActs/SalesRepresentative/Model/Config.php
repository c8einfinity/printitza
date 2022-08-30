<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Config extends \Magento\Framework\DataObject
{

    const XML_PATH_ENABLE_FRONTEND = 'brainacts_salesrep/general/enabled';

    const XML_PATH_ALLOW_CHOOSE = 'brainacts_salesrep/general/manually_select';

    const XML_PATH_REPORT_ROLE = 'brainacts_salesrep/admin/report_access_role';

    const XML_PATH_RESTRICT_ORDER = 'brainacts_salesrep/admin/view_only_owner';

    const XML_PATH_ROLE_FULL_ACCESS = 'brainacts_salesrep/admin/report_access_role';

    const XML_PATH_AUTO_ASSIGN = 'brainacts_salesrep/admin/auto_assign';

    const XML_PATH_MANUALLY_ASSIGN = 'brainacts_salesrep/admin/manually_assign';

    const XML_PATH_ALLOW_CHANGE = 'brainacts_salesrep/general/manually_change';

    const XML_PATH_CHANGE_ADMIN_APPROVE = 'brainacts_salesrep/general/admin_approve';

    //DASHBOARD
    const XML_PATH_DASHBOARD_WITHDRAWAL = 'brainacts_salesrep/dashboard/withdrawal';

    const XML_PATH_DASHBOARD_HOLD_PERIOD = 'brainacts_salesrep/dashboard/hold_period';

    //COMMISSION
    const XML_PATH_COMMISSIONS_USE_COST = 'brainacts_salesrep/commission/use_cost';

    const XML_PATH_COMMISSIONS_COST_ATTRIBUTE = 'brainacts_salesrep/commission/cost_attribute';

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth\Session $adminSession,
        array $data = []
    ) {
    
        $this->scopeConfig = $scopeConfig;
        $this->session = $adminSession;
        parent::__construct($data);
    }

    /**
     *
     * @param null $store
     * @return bool
     */
    public function isActiveFront($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_FRONTEND,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     *
     * @param null $store
     * @return bool
     */
    public function isAutoAssignAdmin($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AUTO_ASSIGN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return id of role that have full access to reports
     * @return int
     */
    public function getReportRole()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REPORT_ROLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isRestrictOrder()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_RESTRICT_ORDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return array with roles that have access to restricted areas
     * @return array
     */
    public function getRoleFullAccess()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_ROLE_FULL_ACCESS,
            ScopeInterface::SCOPE_STORE
        );

        if ($value !== null && is_string($value)) {
            $value = explode(',', $value);
        } else {
            $value = [];
        }

        return $value;
    }

    /**
     * Return is allowed choose sales representative at frontend
     * @return bool
     */
    public function isEnabledChoose()
    {
        $enableOnFront = $this->isActiveFront();

        $allowChoose = (bool)$this->scopeConfig->isSetFlag(
            self::XML_PATH_ALLOW_CHOOSE,
            ScopeInterface::SCOPE_STORE,
            null
        );

        if ($enableOnFront && $allowChoose) {
            return true;
        }
        return false;
    }

    /**
     * Return status if allow to change SR at the frontend
     * @return bool
     */
    public function isAllowChange()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ALLOW_CHANGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if allow to change SR at frontend withounr admin confimation
     * @return bool
     */
    public function adminConfirmChange()
    {
        return !(bool)$this->scopeConfig->getValue(
            self::XML_PATH_CHANGE_ADMIN_APPROVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isAllowAssign()
    {
        $flag = (bool)$this->scopeConfig->getValue(
            self::XML_PATH_MANUALLY_ASSIGN,
            ScopeInterface::SCOPE_STORE
        );

        $role = $this->session->getUser()->getRole()->getId();
        $roles = $this->getRoleFullAccess();
        if (in_array($role, $roles)) {
            return true;
        }

        return $flag;
    }

    /**
     * Return flag is Withdrawal is allowed
     * @return bool
     */
    public function isWithdrawalActive()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DASHBOARD_WITHDRAWAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getHoldPeriod()
    {
        $period = (int)$this->scopeConfig->getValue(
            self::XML_PATH_DASHBOARD_HOLD_PERIOD,
            ScopeInterface::SCOPE_STORE
        );
        return $period;
    }

    /**
     * Check should we use cost attribute for calculate commission
     * @return bool
     */
    public function useCostInCalculation()
    {
        $flag = $this->scopeConfig->isSetFlag(
            self::XML_PATH_COMMISSIONS_USE_COST,
            ScopeInterface::SCOPE_STORE
        );

        return $flag ? $this->getCostAttribute() : false;
    }

    /**
     * Get Cost Attribute
     * @return mixed
     */
    public function getCostAttribute()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COMMISSIONS_COST_ATTRIBUTE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
