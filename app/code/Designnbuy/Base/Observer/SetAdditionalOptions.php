<?php

namespace Designnbuy\Base\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;

class SetAdditionalOptions implements ObserverInterface
{

    protected $_request;


    private $_serializer;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request, Json $serializer){
        $this->_request = $request;
        $this->_serializer = $serializer;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($this->_request->getFullActionName() == 'catalog_product_view') { //checking when product is adding to cart
            //$observer->getProduct()->setAllowScratch(0);
        }


    }
}