<?php

namespace Designnbuy\Designidea\Model\Designidea;

use Designnbuy\Designidea\Model\Designidea;
use Designnbuy\Designidea\Model\DesignideaUrlPathGenerator;
use Designnbuy\Designidea\Model\DesignideaUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

class CanonicalUrlRewriteGenerator
{
    /** @var DesignideaUrlPathGenerator */
    protected $designideaUrlPathGenerator;

    /** @var UrlRewriteFactory */
    protected $urlRewriteFactory;

    /**
     * @param DesignideaUrlPathGenerator $designideaUrlPathGenerator
     * @param UrlRewriteFactory $urlRewriteFactory
     */
    public function __construct(DesignideaUrlPathGenerator $designideaUrlPathGenerator, UrlRewriteFactory $urlRewriteFactory)
    {
        $this->designideaUrlPathGenerator = $designideaUrlPathGenerator;
        $this->urlRewriteFactory = $urlRewriteFactory;
    }

    /**
     * Generate list based on store view
     *
     * @param int $storeId
     * @param Designidea $designidea
     * @return UrlRewrite[]
     */
    public function generate($storeId, Designidea $designidea)
    {
        return [
            $this->urlRewriteFactory->create()
                ->setEntityType(DesignideaUrlRewriteGenerator::ENTITY_TYPE)
                ->setEntityId($designidea->getId())
                ->setRequestPath($this->designideaUrlPathGenerator->getUrlPathWithSuffix($designidea, $storeId))
                ->setTargetPath($this->designideaUrlPathGenerator->getCanonicalUrlPath($designidea))
                ->setStoreId($storeId)
        ];
    }
}
