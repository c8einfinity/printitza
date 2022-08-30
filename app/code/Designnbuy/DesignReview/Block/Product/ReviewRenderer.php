<?php
/**
 * Review renderer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block\Product;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;

class ReviewRenderer extends \Magento\Framework\View\Element\Template /*implements ReviewRendererInterface*/
{
    const SHORT_VIEW = 'short';
    const FULL_VIEW = 'default';
    const DEFAULT_VIEW = self::FULL_VIEW;
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'helper/summary.phtml',
        self::SHORT_VIEW => 'helper/summary_short.phtml',
    ];

    /**
     * Review model factory
     *
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_reviewFactory = $reviewFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }


    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    /*public function getProduct()
    {
        if ($this->_coreRegistry->registry('current_designer_design_view')) {
            return $this->_coreRegistry->registry('current_designer_design_view');
        }
        return;

    }*/

    public function getDesign()
    {
        return $this->_coreRegistry->registry('current_designer_design_view');
    }

    /**
     * Get review summary html
     *
     * @param Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     *
     * @return string
     */
    public function getReviewsSummaryHtml(
        $product,
        $templateType = self::DEFAULT_VIEW,
        $displayIfNoReviews = false
    ) {
        $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());

        /*if (!$product->getRatingSummary() && !$displayIfNoReviews) {
            return '';
        }*/
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = self::DEFAULT_VIEW;
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        $this->setProduct($product);

        return $this->toHtml();
    }

    /**
     * Get ratings summary
     *
     * @return string
     */
    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    /**
     * Get count of reviews
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

    /**
     * Get review product list url
     *
     * @param bool $useDirectLink allows to use direct link for product reviews page
     * @return string
     */
    public function getReviewsUrl($useDirectLink = false)
    {
        $product = $this->getProduct();
        /*return $this->getUrl(
            'designreview/product/list',
            ['id' => $product->getId()]
        );*/
        if ($useDirectLink) {
            return $this->getUrl(
                'designreview/product/list',
                ['id' => $product->getId()]
            );
        }
        $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }
}
