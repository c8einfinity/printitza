<?php
namespace Designnbuy\Base\Plugin\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
class Item
{
    public function aroundRepresentProduct(QuoteItem $subject, \Closure $proceed, $product)
    {
        return false;
    }
}