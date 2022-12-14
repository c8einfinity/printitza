<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Controller\Adminhtml\Product;

use Designnbuy\DesignReview\Controller\Adminhtml\Product as ProductController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Designnbuy\DesignReview\Model\ReviewFactory;
use Designnbuy\DesignReview\Model\RatingFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;

class RatingItems extends ProductController
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     * @param \Designnbuy\DesignReview\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context, $coreRegistry, $reviewFactory, $ratingFactory);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $layout = $this->layoutFactory->create();
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents(
            $layout->createBlock(\Designnbuy\DesignReview\Block\Adminhtml\Rating\Detailed::class)
                ->setIndependentMode()
                ->toHtml()
        );
        return $resultRaw;
    }
}
