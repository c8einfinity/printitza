<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;


class Addtocart extends \Magento\Framework\App\Action\Action
{
    
    protected $_helper;

    protected $outputHelper;
    
    protected $_cart;

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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Json $serializer = null,
        \Magento\Catalog\Model\Product $productRepository
    ) {
        $this->_helper = $_helper;
        $this->outputHelper = $outputHelper;
        $this->_cart = $_cart;
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
        /* echo "<pre>"; print_r($request);
        echo "<pre>"; print_r($productParams); exit; */
        try{
            if(isset($productParams['product']) && $productParams['product'] != ''){
                
                if (isset($request['qty']) && $request['qty'] >= 1) {
                    $productId = $productParams['product'];
                    $product = $this->productRepository->load($productId);

                    
                    $params = array();      
                    $options = array();
                    $params['qty'] = 1;
                    $params['product'] = $productId;

                    $params['photoAlbum'] = true;
                    $params['photoAlbum_FileName'] = 'photo_album_'.time().'_'.$product->getId().'_'.count($request['photos']);
                    
                    $additionalOptions = [];
                    $additionalCustomOptions = [];
                    $photoQty = [];
                    $cart_photo_id = [];
                    if(isset($request['photos']) && !empty($request['photos'])){
                        foreach($request['photos'] as $photo_options){
                            if(isset($photo_options['photo_qty']) && $photo_options['photo_qty'] != ""){
                                $additionalOptions[] = array(
                                    'label' => "Photo",
                                    'value' => $this->_helper->getPhotosById($photo_options['photo_id']),
                                );
                                $additionalOptions[] = array(
                                    'label' => "Quantity",
                                    'value' => $photo_options['photo_qty'],
                                );
                                $photoQty[] = $photo_options['photo_qty'];
                                $cart_photo_id[] = $photo_options['photo_id'];
                            } else {
                                return $resultJson->setData(
                                    [
                                        'success' => false,
                                        'message' => "Please specify photo quantity."
                                    ]
                                );
                            }
                            
                            if(isset($photo_options['album_custom_option']) && !empty($photo_options['album_custom_option'])){
                                foreach($photo_options['album_custom_option'] as $album_custom_options){
                                    if($album_custom_options != ""){
                                        $split_custom_option = explode("||",$album_custom_options);
                                        if(isset($split_custom_option[1]) && $split_custom_option[1] != ""){
                                            $opt = preg_replace('/[^A-Za-z0-9\+]/', '', $split_custom_option[1]);
                                            $additionalOptions[] = array(
                                                'label' => $split_custom_option[0],
                                                'value' => $opt,
                                            );
                                            $additionalCustomOptions[] = $split_custom_option[0];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $params['cart_photo_id'] = json_encode($cart_photo_id);
                     
                    /* echo "<pre>"; print_r($params); exit; */
                    
                    $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                    $productPrice = $product->getFinalPrice();
                    
                    if(isset($request['selected_custom_option']) && !empty($request['selected_custom_option'])){
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
                        
                    }
                    $params['selected_custom_price'] = $productPrice;
                    

                    $product->setPrice($productPrice);
                    $this->_cart->addProduct($product, $params);
                    $this->_cart->save();
                     
                    return $resultJson->setData(
                        [
                            'success' => true,
                        ]
                    );
                } else {
                    return $resultJson->setData(
                        [
                            'success' => false,
                            'message' => "Please specify number of copy."
                        ]
                    );
                }

            } else {
                return $resultJson->setData(
                    [
                        'success' => false,
                        'message' => "We can't add this item to your shopping cart right now."
                    ]
                );    
            }

        }catch (\Exception $e) {
            return $resultJson->setData(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }
        
        
    }
}