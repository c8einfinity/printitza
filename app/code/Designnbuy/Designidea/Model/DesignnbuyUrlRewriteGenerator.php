<?php

namespace Designnbuy\Designidea\Model;

use Designnbuy\Designidea\Model\Designidea;
use Designnbuy\Designidea\Model\Designidea\CanonicalUrlRewriteGenerator;
use Designnbuy\Designidea\Model\Designidea\CurrentUrlRewritesRegenerator;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory;
use Magento\Store\Model\Store;

class DesignideaUrlRewriteGenerator
{
    /**
     * Entity type code
     */
    const ENTITY_TYPE = 'designnbuy_designidea';

    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Designnbuy\Designidea\Model\Designidea */
    protected $designidea;

    /** @var \Designnbuy\Designidea\Model\Designidea\CurrentUrlRewritesRegenerator */
    protected $currentUrlRewritesRegenerator;

    /** @var \Designnbuy\Designidea\Model\Designidea\CanonicalUrlRewriteGenerator */
    protected $canonicalUrlRewriteGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory */
    protected $objectRegistryFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /**
     * @param \Designnbuy\Designidea\Model\Designidea\CanonicalUrlRewriteGenerator $canonicalUrlRewriteGenerator
     * @param \Designnbuy\Designidea\Model\Designidea\CurrentUrlRewritesRegenerator $currentUrlRewritesRegenerator
     * @param \Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory $objectRegistryFactory
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        CanonicalUrlRewriteGenerator $canonicalUrlRewriteGenerator,
        CurrentUrlRewritesRegenerator $currentUrlRewritesRegenerator,
        ObjectRegistryFactory $objectRegistryFactory,
        StoreViewService $storeViewService,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->canonicalUrlRewriteGenerator = $canonicalUrlRewriteGenerator;
        $this->currentUrlRewritesRegenerator = $currentUrlRewritesRegenerator;
        $this->objectRegistryFactory = $objectRegistryFactory;
        $this->storeViewService = $storeViewService;
        $this->storeManager = $storeManager;
    }

    /**
     * Generate designidea url rewrites
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate(Designidea $designidea)
    {
        $this->designidea = $designidea;
        $storeId = $this->designidea->getStoreId();

        $urls = $this->isGlobalScope($storeId)
            ? $this->generateForGlobalScope()
            : $this->generateForSpecificStoreView($storeId);

        $this->designidea = null;
        return $urls;
    }

    /**
     * Check is global scope
     *
     * @param int|null $storeId
     * @return bool
     */
    protected function isGlobalScope($storeId)
    {
        return null === $storeId || $storeId == Store::DEFAULT_STORE_ID;
    }

    /**
     * Generate list of urls for global scope
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForGlobalScope()
    {
        $urls = [];
        $designideaId = $this->designidea->getId();
        foreach ($this->designidea->getStoreIds() as $storeId) {
            if (!$this->isGlobalScope($storeId)
                && !$this->storeViewService->doesEntityHaveOverriddenUrlKeyForStore($storeId, $designideaId, Designidea::ENTITY)
            ) {
                $urls = array_merge($urls, $this->generateForSpecificStoreView($storeId));
            }
        }
        return $urls;
    }

    /**
     * Generate list of urls for specific store view
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForSpecificStoreView($storeId)
    {
        /**
         * @var $urls \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
         */
        $urls = array_merge(
            $this->canonicalUrlRewriteGenerator->generate($storeId, $this->designidea),
            $this->currentUrlRewritesRegenerator->generate($storeId, $this->designidea)
        );

        /* Reduce duplicates. Last wins */
        $result = [];
        foreach ($urls as $url) {
            $result[$url->getTargetPath() . '-' . $url->getStoreId()] = $url;
        }
        return $result;
    }
}
