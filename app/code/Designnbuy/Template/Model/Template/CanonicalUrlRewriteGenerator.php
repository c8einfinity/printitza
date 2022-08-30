<?php

namespace Designnbuy\Template\Model\Template;

use Designnbuy\Template\Model\Template;
use Designnbuy\Template\Model\TemplateUrlPathGenerator;
use Designnbuy\Template\Model\TemplateUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

class CanonicalUrlRewriteGenerator
{
    /** @var TemplateUrlPathGenerator */
    protected $templateUrlPathGenerator;

    /** @var UrlRewriteFactory */
    protected $urlRewriteFactory;

    /**
     * @param TemplateUrlPathGenerator $templateUrlPathGenerator
     * @param UrlRewriteFactory $urlRewriteFactory
     */
    public function __construct(TemplateUrlPathGenerator $templateUrlPathGenerator, UrlRewriteFactory $urlRewriteFactory)
    {
        $this->templateUrlPathGenerator = $templateUrlPathGenerator;
        $this->urlRewriteFactory = $urlRewriteFactory;
    }

    /**
     * Generate list based on store view
     *
     * @param int $storeId
     * @param Template $template
     * @return UrlRewrite[]
     */
    public function generate($storeId, Template $template)
    {
        return [
            $this->urlRewriteFactory->create()
                ->setEntityType(TemplateUrlRewriteGenerator::ENTITY_TYPE)
                ->setEntityId($template->getId())
                ->setRequestPath($this->templateUrlPathGenerator->getUrlPathWithSuffix($template, $storeId))
                ->setTargetPath($this->templateUrlPathGenerator->getCanonicalUrlPath($template))
                ->setStoreId($storeId)
        ];
    }
}
