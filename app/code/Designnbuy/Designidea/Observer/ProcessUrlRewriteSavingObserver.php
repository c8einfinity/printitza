<?php

namespace Designnbuy\Designidea\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Designnbuy\Designidea\Model\DesignideaUrlRewriteGenerator;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var \Designnbuy\Designidea\Model\DesignideaUrlRewriteGenerator
     */
    protected $designideaUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @param \Designnbuy\Designidea\Model\DesignideaUrlRewriteGenerator $designideaUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(DesignideaUrlRewriteGenerator $designideaUrlRewriteGenerator, UrlPersistInterface $urlPersist)
    {
        $this->designideaUrlRewriteGenerator = $designideaUrlRewriteGenerator;
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
        /** @var $designidea \Designnbuy\Designidea\Model\Designidea */
        $designidea = $observer->getEvent()->getDesignidea();
        if ($designidea->dataHasChangedFor('url_key')) {
            $urls = $this->designideaUrlRewriteGenerator->generate($designidea);
            $this->urlPersist->replace($urls);
        }
    }
}
