<?php

namespace Designnbuy\Notifications\Controller\Adminhtml;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\App\Action\Context;

/**
 * Abstract admin controller
 */
abstract class Actions extends \Magento\Backend\App\Action
{
    const SET_AS_READ = 1;
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey;

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey;

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass;

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu;

    /**
     * Store config section key
     * @var string
     */
    protected $_configSection;

    /**
     * Request id key
     * @var string
     */
    protected $_idKey = 'id';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_read';

    /**
     * Save request params key
     * @var string
     */
    protected $_paramsHolder;

    /**
     * Model Object
     * @var \Magento\Framework\Model\AbstractModel
     */
    protected $_model;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    protected $customerFactory; 
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param Action\Context $context
     * @param DesignerDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     */

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Action execute
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $_preparedActions = ['index', 'grid', 'delete', 'massRead', 'setRead', 'deleteRecord'];
        $_action = $this->getRequest()->getActionName();
        if (in_array($_action, $_preparedActions)) {
            $method = '_'.$_action.'Action';

            $this->_beforeAction();
            $this->$method();
            $this->_afterAction();
        }
    }

    /**
     * Index action
     * @return void
     */
    protected function _indexAction()
    {
        if ($this->getRequest()->getParam('ajax')) 
        {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu($this->_activeMenu);
        $title = __('Manage %1', $this->_getModel(false)->getOwnTitle(true));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_addBreadcrumb($title, $title);
        $this->_view->renderLayout();
    }

    /**
     * Grid action
     * @return void
     */
    protected function _gridAction() 
    {        
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }


    /**
     * Retrieve model name
     * @param  boolean $plural
     * @return string
     */
    protected function _getModelName(\Magento\Framework\Model\AbstractModel $model)
    {
        return $model->getName() ?: $model->getTitle();
    }


    /**
     * Before model Save action
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
    }

    /**
     * After model action
     * @return void
     */
    protected function _afterSave($model, $request)
    {
    }

    /**
     * Before action
     * @return void
     */
    protected function _beforeAction()
    {
    }

    /**
     * After action
     * @return void
     */
    protected function _afterAction()
    {
    }

    /**
     * Delete action
     * @return void
     */
    protected function _deleteAction()
    {
        $ids = $this->getRequest()->getParam($this->_idKey);

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $error = false;
        try {
            foreach ($ids as $id) {
                $this->_objectManager->create($this->_modelClass)->setId($id)->delete();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException(
                $e,
                __(
                    "We can't delete %1 right now. %2",
                    strtolower($this->_getModel(false)->getOwnTitle()),
                    $e->getMessage()
                )
            );
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __('%1 have been deleted.', $this->_getModel(false)->getOwnTitle(count($ids) > 1))
            );
        }

        $this->_redirect('*/*');
    }

    /**
     * Change status action
     * @return void
     */
    protected function _massReadAction()
    {
        $ids = $this->getRequest()->getParam($this->_idKey);

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $model = $this->_getModel(false);

        $error = false;

        try {
            $status = $this->getRequest()->getParam('status');

            $statusFieldName = $this->_statusField;

            if (is_null($status)) {
                throw new \Exception(__('Parameter "Status" missing in request data.'));
            }

            if (is_null($statusFieldName)) {
                throw new \Exception(__('Status Field Name is not specified.'));
            }

            foreach ($ids as $id) 
            {
                $this->_objectManager->create($this->_modelClass)
                    ->load($id)
                    ->setData($this->_statusField, $status)
                    ->save();                
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException(
                $e,
                __(
                    "We can't change status of %1 right now. %2",
                    strtolower($model->getOwnTitle()),
                    $e->getMessage()
                )
            );
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __('%1 status have been changed.', $model->getOwnTitle(count($ids) > 1))
            );
        }

        $this->_redirect('*/*');
    }

    protected function _setReadAction()
    {
        $model = $this->_getModel(false);

        $error = false;

        try {
            $id = $this->getRequest()->getParam('id');
            $statusFieldName = $this->_statusField;

            if (is_null($id)) {
                throw new \Exception(__('Parameter "Status" missing in request data.'));
            }

            if (is_null($statusFieldName)) {
                throw new \Exception(__('Status Field Name is not specified.'));
            }
            
            $this->_objectManager->create($this->_modelClass)
                ->load($id)
                ->setData($this->_statusField, self::SET_AS_READ)
                ->save();                
           
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException(
                $e,
                __(
                    "We can't change status of %1 right now. %2",
                    strtolower($model->getOwnTitle()),
                    $e->getMessage()
                )
            );
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __('%1 status have been changed.', $model->getOwnTitle(count($id) > 1))
            );
        }

        $this->_redirect('*/*');
    }

    /**
     * Delete action
     * @return void
     */
    protected function _deleteRecordAction()
    {
        $error = false;
        try {
            $id = $this->getRequest()->getParam('id');
            $this->_objectManager->create($this->_modelClass)->setId($id)->delete();
            
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException(
                $e,
                __(
                    "We can't delete %1 right now. %2",
                    strtolower($this->_getModel(false)->getOwnTitle()),
                    $e->getMessage()
                )
            );
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __('%1 have been deleted.', $this->_getModel(false)->getOwnTitle(count($id) > 1))
            );
        }
        $this->_redirect('*/*');
    }


    /**
     * Filter request params
     * @param  array $data
     * @return array
     */
    protected function filterParams($data)
    {
        return $data;
    }

    /**
     * Get core registry
     * @return void
     */
    protected function _getRegistry()
    {
        if (is_null($this->_coreRegistry)) {
            $this->_coreRegistry = $this->_objectManager->get('\Magento\Framework\Registry');
        }
        return $this->_coreRegistry;
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->_allowedKey);
    }

    /**
     * Retrieve model object
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function _getModel($load = true)
    {
        if (is_null($this->_model)) {
            $this->_model = $this->_objectManager->create($this->_modelClass);

            $id = (int)$this->getRequest()->getParam($this->_idKey);
            if ($id && $load) {
                $this->_model->load($id);
            }
        }
        return $this->_model;
    }
}
