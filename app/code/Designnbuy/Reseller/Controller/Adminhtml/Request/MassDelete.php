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
namespace Designnbuy\Reseller\Controller\Adminhtml\Request;

class MassDelete extends \Designnbuy\Reseller\Controller\Adminhtml\Request\MassAction
{
    /**
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @return $this
     */
    protected function massAction(\Designnbuy\Reseller\Api\Data\RequestInterface $request)
    {
        $this->requestRepository->delete($request);
        return $this;
    }
}
