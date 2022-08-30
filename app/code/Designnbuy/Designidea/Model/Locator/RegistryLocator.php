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

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Designnbuy\Designidea\Api\Data\DesignideaInterface;

/**
 * Registry Locator for Designidea
 *
 * @category Designnbuy
 * @package  Designnbuy\Designidea
 * @author   Ajay Makwana
 */
class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var DesignideaInterface
     */
    private $designIdea;
    /**
     * @var CategoryInterface
     */
    private $category;
    /**
     * @var StoreInterface
     */
    private $store;
    /**
     * @param Registry $registry The application registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundException
     */
    public function getDesignIdea()
    {
        if (null !== $this->designIdea) {
            return $this->designIdea;
        }

        if ($this->registry->registry('current_designidea')) {
            return $this->designIdea = $this->registry->registry('current_designidea');
        }

        throw new NotFoundException(__('Editable artwork was not registered'));
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     */
    public function getStore()
    {
        if (null !== $this->store) {
            return $this->store;
        }

        if ($store = $this->registry->registry('current_store')) {
            return $this->store = $store;
        }

        throw new NotFoundException(__('Store was not registered'));
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundException
     */
    public function getDesignIdeaCategory()
    {
        if (null !== $this->category) {
            return $this->category;
        }

        if ($this->registry->registry('current_category')) {
            return $this->category = $this->registry->registry('current_category');
        }

        throw new NotFoundException(__('Category was not registered'));
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryWebsiteIds()
    {
        return $this->getDesignIdeaCategory()->getWebsiteIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignIdeaWebsiteIds()
    {
        return $this->getDesignIdea()->getWebsiteIds();
    }
}
