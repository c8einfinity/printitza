<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;


class calculatePrice extends \Magento\Framework\App\Action\Action
{
    
    protected $_helper;

    protected $outputHelper;
    
    protected $_cart;
    
    protected $priceHelper;

    /**
     * @var Json
     */
    private $serializer;

    private $resultJsonFactory;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $_helper,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Checkout\Model\Cart $_cart,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Json $serializer = null,
        \Magento\Catalog\Model\Product $productRepository
    ) {
        $this->_helper = $_helper;
        $this->outputHelper = $outputHelper;
        $this->_cart = $_cart;
        $this->priceHelper = $priceHelper;
        $this->resultJsonFactory=$resultJsonFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    public function execute()
    {	
        $resultJson = $this->resultJsonFactory->create();
        $request = $this->getRequest()->getParams();
        $productParams = $this->_helper->getProductParams();

        if(isset($request['selected_custom_option']) && !empty($request['selected_custom_option'])){
            $productId = $productParams['product'];
            $product = $this->productRepository->load($productId);
            $productPrice = $product->getFinalPrice();
            foreach($request['selected_custom_option'] as $key => $calculateOption){
                $exploadedOption = explode("||",$calculateOption);
                $optionid = null;
                $optionval = null;
                $optionqty = null;
                if(isset($exploadedOption[0])){
                    $optionid = $exploadedOption[0];
                }
                if(isset($exploadedOption[1])){
                    $optionval = $exploadedOption[1];
                }
                if(isset($exploadedOption[2])){
                    $optionqty = $exploadedOption[2];
                }
                if($optionid != null && $optionval != null && $optionqty != null){
                    $loadedOption = $product->getOptionById($optionid);
                    $loadedValue = $loadedOption->getValueById($optionval);
                    if ($loadedValue->getPrice() && $loadedValue->getPrice() != 0 && $loadedValue->getPrice() != "" && $loadedValue->getPriceType() && $loadedValue->getPriceType() != ""){
                        
                        if($loadedValue->getPriceType() == 'percent'){
                            $productPrice += (($product->getPrice() * $loadedValue->getPrice()) / 100 ) * $optionqty;
                        } else {
                            $productPrice += ($loadedValue->getPrice()) * $optionqty;
                        }
                    }
            
                }
            }
            return $resultJson->setData(
                [
                    'success' => $this->priceHelper->currency($productPrice, true, false)
                ]
            );
        }   
    }
}