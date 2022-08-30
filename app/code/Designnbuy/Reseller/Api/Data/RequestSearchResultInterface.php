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
namespace Designnbuy\Reseller\Api\Data;

/**
 * @api
 */
interface RequestSearchResultInterface
{
    /**
     * Get Requests list.
     *
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface[]
     */
    public function getItems();

    /**
     * Set Requests list.
     *
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
