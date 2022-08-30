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

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Designnbuy\Template\Api\Data\TemplateInterface;

/**
 * Registry Locator for Template
 *
 * @category Designnbuy
 * @package  Designnbuy\Template
 * @author   Ajay Makwana
 */
class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var TemplateInterface
     */
    private $template;
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
    public function getTemplate()
    {
        if (null !== $this->template) {
            return $this->template;
        }

        if ($this->registry->registry('current_template')) {
            return $this->template = $this->registry->registry('current_template');
        }

        throw new NotFoundException(__('Template was not registered'));
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
    public function getTemplateCategory()
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
        return $this->getTemplateCategory()->getWebsiteIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateWebsiteIds()
    {
        return $this->getTemplate()->getWebsiteIds();
    }
}
