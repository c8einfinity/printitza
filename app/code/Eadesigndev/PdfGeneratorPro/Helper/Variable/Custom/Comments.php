<?php

namespace Eadesigndev\PdfGeneratorPro\Helper\Variable\Custom;

use Magento\Sales\Block\Order\Creditmemo;
use Magento\Sales\Model\Order;
use Magento\Tax\Helper\Data as TaxData;

class Comments
{
    /**
     * @var Order|Order\Invoice|Creditmemo
     */
    private $source;

    /**
     * @var TaxData
     */
    private $taxData;

    /**
     * Tax constructor.
     * @param TaxData $taxData
     */
    public function __construct(TaxData $taxData)
    {
        $this->taxData = $taxData;
    }

    /**
     * @param $source
     * @return $this
     */
    public function entity($source)
    {
        if (is_object($source)) {
            $this->source = $source;
            $this->addComments();
            return $this;
        }
    }

    /**
     * @return $this
     */
    public function addComments()
    {
        $commentsCollection =  $this->source->getCommentsCollection();

        $commentString = '';

        if (!empty($commentsCollection) && is_object($commentsCollection)) {
            foreach ($commentsCollection->getItems() as $comment) {
                $commentString .= $comment->getData('comment') . '<br>';
            }
        }

        $this->source->setData('comments', $commentString);

        return $this;
    }
}
