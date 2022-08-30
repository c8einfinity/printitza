<?php

namespace Designnbuy\Template\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Designnbuy\Template\Model\TemplateUrlRewriteGenerator;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var \Designnbuy\Template\Model\TemplateUrlRewriteGenerator
     */
    protected $templateUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @param \Designnbuy\Template\Model\TemplateUrlRewriteGenerator $templateUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(TemplateUrlRewriteGenerator $templateUrlRewriteGenerator, UrlPersistInterface $urlPersist)
    {
        $this->templateUrlRewriteGenerator = $templateUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        echo "<pre>";
        print_r(12121);
        die;
        /** @var $template \Designnbuy\Template\Model\Template */
        $template = $observer->getEvent()->getTemplate();
        if ($template->dataHasChangedFor('url_key')) {
            $urls = $this->templateUrlRewriteGenerator->generate($template);
            $this->urlPersist->replace($urls);
        }
    }
}
