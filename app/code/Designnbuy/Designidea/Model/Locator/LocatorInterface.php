<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Offer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Designnbuy\Designidea\Model\Locator;

use Designnbuy\Designidea\Api\Data\DesignideaInterface;
use Designnbuy\Designidea\Api\Data\Category\CategoryInterface;

/**
 * Designidea Locator Interface
 *
 * @category Designnbuy
 * @package  Designnbuy\Designidea
 * @author   Ajay Makwana
 */
interface LocatorInterface
{
    /**
     * @return DesignideaInterface
     */
    public function getDesignIdea();

    /**
     * @return CategoryInterface
     */
    public function getDesignIdeaCategory();

    /**
     * @return array
     */
    public function getCategoryWebsiteIds();

    /**
     * @return array
     */
    public function getDesignIdeaWebsiteIds();
}
