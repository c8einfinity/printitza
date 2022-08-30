<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    protected $_skipObjectRestriction = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Amasty\Base\Helper\Utils
     */
    protected $baseUtils;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ResponseInterface $response,
        \Amasty\Base\Helper\Utils $baseUtils
    ) {
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;
        $this->messageManager = $messageManager;
        $this->_backendUrl = $backendUrl;
        $this->_response = $response;
        $this->baseUtils = $baseUtils;

        return parent::__construct($context);
    }

    /**
     * @return \Amasty\Rolepermissions\Model\Rule
     */
    public function currentRule()
    {
        if (($rule = $this->_coreRegistry->registry('current_amrolepermissions_rule')) == null) {

            $user = $this->_authSession->getUser();

            if (!$user)
                return false;

            $rule = $this->_objectManager->create('Amasty\Rolepermissions\Model\Rule')->loadByRole($user->getRole()->getId());
            $this->_coreRegistry->register('current_amrolepermissions_rule', $rule, true);
        }

        return $rule;
    }

    public function redirectHome()
    {
        if (!$this->_authSession->getUser())
            return;

        $this->messageManager->addError(__('Access Denied'));

        if ($this->_request->getActionName() == 'index') {
            $page = $this->_backendUrl->getStartupPageUrl();

            $url = $this->_backendUrl->getUrl($page);
        }
        else {
            $url = $this->_backendUrl->getUrl('*/*');
        }

        $this->_response
            ->setRedirect($url)
            ->sendResponse();

        $this->baseUtils->_exit(0);
    }

    public function restrictObjectByStores($data)
    {
        list($name, $value, $isWebsite) = $this->_getRelationField($data);
        if ($value) {
            $rule = $this->currentRule();

            if ($isWebsite) {
                $allowedIds = $rule->getPartiallyAccessibleWebsites();
            }
            else {
                $allowedIds = $rule->getScopeStoreviews();
            }

            if (!is_array($value)) {
                $value = explode(',', $value);
            }

            if (($value != [0]) && !array_intersect($value, $allowedIds)) {
                $this->redirectHome();
            }
        }

        return $this;
    }

    public function alterObjectStores($object)
    {
        list($name, $value, $isWebsite) = $this->_getRelationField($object->getData());
        if ($value) {
            if (!is_array($value)) {
                $value = explode(',', $value);
                $array = false;
            }
            else {
                $array = true;
            }

            if ($object->getId()) {
                list($origName, $origValue, $isWebsite) = $this->_getRelationField($object->getOrigData());

                if ($origName === null) {
                    $oldObject = clone $object;
                    $oldObject->load($object->getId());

                    list($origName, $origValue, $isWebsite) = $this->_getRelationField($oldObject->getOrigData());
                }

                if (!is_array($origValue)) {
                    $origValue = explode(',', $origValue);
                }
            }
            else {
                $origValue = [];
            }

            if ($value != $origValue) {
                $rule = $this->currentRule();

                if ($isWebsite) {
                    $allowedIds = $rule->getPartiallyAccessibleWebsites();
                }
                else {
                    $allowedIds = $rule->getScopeStoreviews();
                }

                $newValue = $this->combine($origValue, $value, $allowedIds);

                if (!$array) {
                    $newValue = implode(',', array_filter($newValue));
                }

                $object->setData($name, $newValue);
            }
        }

        return $this;
    }

    public function combine($old, $new, $allowed)
    {
        if (!is_array($old)) {
            $old = [];
        }

        $map = array_flip(array_unique(array_merge($new, $old)));

        foreach ($map as $id => $order) {
            if (in_array($id, $allowed)) {
                if (!in_array($id, $new)) {
                    unset($map[$id]);
                }
            } else {
                if (!in_array($id, $old)) {
                    unset($map[$id]);
                }
            }
        }

        return array_keys($map);
    }

    protected function _getRelationField($data)
    {
        if (!$data)
            return false;

        $fieldNames = [
            'websites', 'website_id', 'website_ids',
            'stores', 'store_id', 'store_ids',
        ];

        foreach ($fieldNames as $name) {
            if (isset($data[$name])) {
                if (substr($name, 0, 7) == 'website')
                    $isWebsite = true;
                else
                    $isWebsite = false;

                return [$name, $data[$name], $isWebsite];
            }
        }
    }

    public function canSkipObjectRestriction()
    {
        if ($this->_skipObjectRestriction === null) {
            $this->_skipObjectRestriction = false;

            $action = $this->_request->getActionName();

            if (in_array($action, ['edit', 'view', 'index', 'render'])) {
                $controller = $this->_request->getControllerName();

                $rule = $this->_coreRegistry->registry('current_amrolepermissions_rule');

                if (
                    (!$rule->getLimitOrders() && ($controller == 'order' || ($this->_request->getParam('namespace') == 'sales_order_grid')))
                    ||
                    (!$rule->getLimitInvoices() && ($controller == 'order_invoice' || $controller == 'order_transactions'))
                    ||
                    (!$rule->getLimitShipments() && $controller == 'order_shipment')
                    ||
                    (!$rule->getLimitMemos() && $controller == 'order_creditmemo')
                )
                {
                    $this->_skipObjectRestriction = true;
                }
            }
        }

        return $this->_skipObjectRestriction;
    }

    public function priceError()
    {
        $this->messageManager->addError(__('Price & Special Price must be greater then main store price'));

        if ($this->_request->getActionName() == 'index') {
            $page = $this->_backendUrl->getStartupPageUrl();

            $url = $this->_backendUrl->getUrl($page);
        }
        else {
            $url = $this->_backendUrl->getUrl('*/*');
        }

        $this->_response
            ->setRedirect($url)
            ->sendResponse();

        $this->baseUtils->_exit(0);
    }
}
