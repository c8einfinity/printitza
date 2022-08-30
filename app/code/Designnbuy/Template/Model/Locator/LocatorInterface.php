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
namespace Designnbuy\Template\Model\Locator;

use Designnbuy\Template\Api\Data\TemplateInterface;
use Designnbuy\Template\Api\Data\Category\CategoryInterface;

/**
 * Template Locator Interface
 *
 * @category Designnbuy
 * @package  Designnbuy\Template
 * @author   Ajay Makwana
 */
interface LocatorInterface
{
    /**
     * @return TemplateInterface
     */
    public function getTemplate();

    /**
     * @return CategoryInterface
     */
    public function getTemplateCategory();

    /**
     * @return array
     */
    public function getCategoryWebsiteIds();

    /**
     * @return array
     */
    public function getTemplateWebsiteIds();
}
