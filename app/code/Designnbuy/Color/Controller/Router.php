<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Controller;

use \Designnbuy\Color\Model\Url;
/**
 * Color Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Designnbuy\Color\Model\ColorFactory
     */
    protected $_colorFactory;

    /**
     * Category factory
     *
     * @var \Designnbuy\Color\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Author factory
     *
     * @var \Designnbuy\Color\Model\AuthorFactory
     */
    protected $_authorFactory;

    /**
     * Tag factory
     *
     * @var \Designnbuy\Color\Model\TagFactory
     */
    protected $_tagFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Designnbuy\Color\Model\Url
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var int
     */
    protected $_colorId;

    /**
     * @var int
     */
    protected $_categoryId;

    /**
     * @var int
     */
    protected $_authorId;

    /**
     * @var int
     */
    protected $_tagId;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Designnbuy\Color\Model\ColorFactory $colorFactory
     * @param \Designnbuy\Color\Model\CategoryFactory $categoryFactory
     * @param \Designnbuy\Color\Model\AuthorFactory $authorFactory
     * @param \Designnbuy\Color\Model\TagFactory $tagFactory
     * @param \Designnbuy\Color\Model\Url $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Url $url,
        \Designnbuy\Color\Model\ColorFactory $colorFactory,
        \Designnbuy\Color\Model\CategoryFactory $categoryFactory,
        \Designnbuy\Color\Model\AuthorFactory $authorFactory,
        \Designnbuy\Color\Model\TagFactory $tagFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_colorFactory = $colorFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_authorFactory = $authorFactory;
        $this->_tagFactory = $tagFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Color Pages and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $_identifier = trim($request->getPathInfo(), '/');

        $pathInfo = explode('/', $_identifier);
        $colorRoute = $this->_url->getRoute();

        if ($pathInfo[0] != $colorRoute) {
            return;
        }
        unset($pathInfo[0]);

        switch ($this->_url->getPermalinkType()) {
            case Url::PERMALINK_TYPE_DEFAULT:
                foreach ($pathInfo as $i => $route) {
                    $pathInfo[$i] = $this->_url->getControllerName($route);
                }
                break;
            case Url::PERMALINK_TYPE_SHORT:
                if (isset($pathInfo[1])) {
                    if ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_SEARCH)) {
                        $pathInfo[1] = Url::CONTROLLER_SEARCH;
                    } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_AUTHOR)) {
                        $pathInfo[1] = Url::CONTROLLER_AUTHOR;
                    } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_TAG)) {
                        $pathInfo[1] = Url::CONTROLLER_TAG;
                    } elseif (count($pathInfo) == 1) {
                        if ($this->_isArchiveIdentifier($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_ARCHIVE;
                        } elseif ($colorId = $this->_getColorId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_COLOR;
                        } elseif ($categoryId = $this->_getCategoryId($pathInfo[1])) {
                            $pathInfo[2] = $pathInfo[1];
                            $pathInfo[1] = Url::CONTROLLER_CATEGORY;
                        }
                    } elseif (count($pathInfo) > 1) {
                        if ($colorId = $this->_getColorId(implode('/', $pathInfo))) {
                            $pathInfo[2] = implode('/', $pathInfo);
                            $pathInfo[1] = Url::CONTROLLER_COLOR;
                        }
                    }
                }
                break;
        }

        $identifier = implode('/', $pathInfo);

        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->_eventManager->dispatch(
            'designnbuy_color_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );

        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Redirect',
                ['request' => $request]
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $identifier = $condition->getIdentifier();

        $success = false;
        $info = explode('/', $identifier);

        if (!$identifier) {
            $request->setModuleName('color')->setControllerName('index')->setActionName('index');
            $success = true;
        } elseif (count($info) > 1) {

            $store = $this->_storeManager->getStore()->getId();

            switch ($info[0]) {
                case Url::CONTROLLER_COLOR :
                    if (!$colorId = $this->_getColorId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_COLOR)
                        ->setActionName('view')
                        ->setParam('id', $colorId);

                    $success = true;
                    break;

                case Url::CONTROLLER_CATEGORY :
                    if (!$categoryId = $this->_getCategoryId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_CATEGORY)
                        ->setActionName('view')
                        ->setParam('id', $categoryId);

                    $success = true;
                    break;

                case Url::CONTROLLER_ARCHIVE :
                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_ARCHIVE)
                        ->setActionName('view')
                        ->setParam('date', $info[1]);

                    $success = true;
                    break;

                case Url::CONTROLLER_AUTHOR :
                    if (!$authorId = $this->_getAuthorId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_AUTHOR)
                        ->setActionName('view')
                        ->setParam('id', $authorId);

                    $success = true;
                    break;

                case Url::CONTROLLER_TAG :
                    if (!$tagId = $this->_getTagId($info[1])) {
                        return null;
                    }

                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_TAG)
                        ->setActionName('view')
                        ->setParam('id', $tagId);

                    $success = true;
                    break;

                case Url::CONTROLLER_SEARCH :
                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_SEARCH)
                        ->setActionName('index')
                        ->setParam('q', $info[1]);

                    $success = true;
                    break;

                case Url::CONTROLLER_RSS :
                    $request->setModuleName('color')
                        ->setControllerName(Url::CONTROLLER_RSS)
                        ->setActionName(
                            isset($info[1]) ? $info[1] : 'index'
                        );

                    $success = true;
                    break;
            }

        }

        if (!$success) {
            return null;
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $_identifier);

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

    /**
     * Retrieve color id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getColorId($identifier)
    {
        if (is_null($this->_colorId)) {
            $color = $this->_colorFactory->create();
            $this->_colorId = $color->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_colorId;
    }

    /**
     * Retrieve category id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getCategoryId($identifier)
    {
        if (is_null($this->_categoryId)) {
            $category = $this->_categoryFactory->create();
            $this->_categoryId = $category->checkIdentifier(
                $identifier,
                $this->_storeManager->getStore()->getId()
            );
        }

        return $this->_categoryId;
    }

    /**
     * Retrieve category id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getAuthorId($identifier)
    {
        if (is_null($this->_authorId)) {
            $author = $this->_authorFactory->create();
            $this->_authorId = $author->checkIdentifier(
                $identifier
            );
        }

        return $this->_authorId;
    }

    /**
     * Retrieve tag id by identifier
     * @param  string $identifier
     * @return int
     */
    protected function _getTagId($identifier)
    {
        if (is_null($this->_tagId)) {
            $tag = $this->_tagFactory->create();
            $this->_tagId = $tag->checkIdentifier(
                $identifier
            );
        }

        return $this->_tagId;
    }

    /**
     * Detect arcive identifier
     * @param  string  $identifier
     * @return boolean
     */
    protected function _isArchiveIdentifier($identifier)
    {
        $info = explode('-', $identifier);
        return count($info) == 2
            && strlen($info[0]) == 4
            && strlen($info[1]) == 2
            && is_numeric($info[0])
            && is_numeric($info[1]);
    }

}
