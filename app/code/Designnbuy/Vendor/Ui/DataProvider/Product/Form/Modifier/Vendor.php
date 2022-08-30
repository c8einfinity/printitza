<?php

namespace Designnbuy\Vendor\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Ui\Component\Form;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Vendor extends AbstractModifier
{
    const SORT_ORDER = 40;

    protected $locator;

    protected $websiteRepository;

    protected $groupRepository;

    protected $storeRepository;

    protected $websitesOptionsList;

    protected $storeManager;

    protected $websitesList;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    private $dataScopeName;

    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        GroupRepositoryInterface $groupRepository,
        StoreRepositoryInterface $storeRepository,
        ArrayManager $arrayManager,
        $dataScopeName
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->storeRepository = $storeRepository;
        $this->dataScopeName = $dataScopeName;
    }

    public function modifyMeta(array $meta)
    {
        if (isset($meta['vendor'])) {
            if($this->locator->getProduct()->getVendorAssignment() == 'manual'){
                unset($meta['vendor']);
            }
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

}