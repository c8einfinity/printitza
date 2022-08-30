<?php
namespace Designnbuy\Orderattachment\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
 
class VersionTwoAttachmentToCartBefore implements ObserverInterface
{
    /**
     * add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    private $serializer;

    public function __construct(
        Json $serializer = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        if ($observer->getData('info')) {
            
            $paramsData = $observer->getData('info');
            if(isset($paramsData['proofing_options'])){
                
                $product = $observer->getData('product');

                if($paramsData['proofing_options'] == 'proof_request'){
                
                    $additionalOptions[] = array(
                        'label' => "Proof Required",
                        'value' => "Yes",
                    );
                    $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));

                } else {
                    
                    $additionalOptions[] = array(
                        'label' => "Proof Required",
                        'value' => "No",
                    );
                    $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));

                }
            }
            
            return $this;
        }
         
 
        return $this;
    }
}