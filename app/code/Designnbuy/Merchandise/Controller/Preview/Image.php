<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Merchandise\Controller\Preview;

use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * SaveStringOnServer Action
 */
class Image extends \Magento\Framework\App\Action\Action
{
    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Customer Helper Class
     *
     * @var \Designnbuy\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */

    private $dnbBaseHelper;

    /**
     * Merchandise
     *
     * @var \Designnbuy\Merchandise\Model\Merchandise
     */
    protected $_merchandise;


    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Designnbuy\Customer\Helper\Data $customerHelper,
        \Designnbuy\Merchandise\Model\Merchandise $merchandise,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->_outputHelper = $outputHelper;
        $this->jsonHelper = $jsonHelper;
        $this->_customerHelper = $customerHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->_merchandise = $merchandise;
        $this->resultRawFactory = $resultRawFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }
    /**
     * SaveStringOnServer action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $request['id'] = $productId = $params['id'];
        $request['customerDetails'] = $customerDetails = $this->_customerHelper->getCustomerDetails();

        $_product = $this->productRepository->getById($productId, false);

        $customProductAttributeSetId = $this->dnbBaseHelper->getCustomProductAttributeSetId();

        $productData = $this->_merchandise->getProductData($request,'front');
        $isMultiColor = $productData['multiColor'];
        $isQuickEditEnable = $this->_merchandise->isQuickEditEnable($_product);

        $designIdeaId = $_product->getDesignideaId();

        $designIdea = $this->_merchandise->getDesignIdeaDesign($designIdeaId);
        $quickEditSvg = array();
        if (isset($designIdea) && !empty($designIdea)) {
            if (isset($designIdea['svg']) && $designIdea['svg'] != '') {
                $quickEditSvg = array($designIdea['svg'][0]);
            } else {
                $isQuickEditEnable = false;
            }
        }
        $request['svg'] =  $quickEditSvg;


        $sides = $productData['sides'];
        $sideImage = '';
        $designArea = [];
        if(count($sides) > 0){
            foreach ($sides as $side) {
                $sideImage = $side['image'];
                $sideAreas = $side['sides_area'];
                foreach ($sideAreas as $sideArea) {
                    $designArea[] = $sideArea['area'];
                    break;
                }
                break;
            }
        }

        $request['configarea'] = json_encode($designArea);

        $attributes = $productData['attributes'];
        $hexColor = '';
        $avalableColor = false;
        if(count($attributes) > 0){
            foreach ($attributes as $attribute) {
                if($attribute['code'] ==  \Designnbuy\Merchandise\Helper\Data::COLOR_FIELD){
                    $avalableColor = true;
                    foreach ($attribute['options'] as $option) {
                        if($isMultiColor == 'yes'){
                            foreach ($option['value'] as $value) {
                                $sideImage = $value['image'];
                                break;
                            }
                        } else {
                            $hexColor = $option['value'];
                            break;
                        }
                    }
                    break;
                }
            }
        }
        $feColorMatrix = '';
        if($hexColor != ''){
            $rgb = $this->hex2rgb($hexColor);
            $feColorMatrix = $this->getFeColorMatrix($rgb);
        }
        $isApplyFilter = true;
        if($isMultiColor == 'yes' || $avalableColor != true){
            $isApplyFilter = false;
        }

        $request['productImageSvg'] = $this->createProductImageSvg($sideImage, $feColorMatrix, $isApplyFilter);

        $output = $this->_outputHelper->generateCustomerPreviewFromDesign($request);

        $stat = $this->_mediaDirectory->stat($output);
        $contentLength = $stat['size'];
        $contentModify = $stat['mtime'];
        $contentType = 'image/png';
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Last-Modified', date('r', $contentModify));
        $resultRaw->setContents($this->_mediaDirectory->readFile($output));
        return $resultRaw;
    }

    public function createProductImageSvg($productSideImage, $feColorMatrixValue, $isApplyFilter)
    {
        $filter = 'url(#colorMat)';
        if(!$isApplyFilter){
            $filter = '';
        }
        list($prodWidth, $prodHeight) = getimagesize($productSideImage);
        $imagewidth = $prodWidth;
		$imageheight = $prodHeight;
		$ratio = null;
		$ratio = $imagewidth / $imageheight;
		if($imagewidth >= $imageheight){
			$imagewidth = 400; 
			$imageheight = $imagewidth / $ratio;
			if($imageheight > 485)	{
				$imageheight = 485; 
				$imagewidth = $ratio * $imageheight;
			}
		}
		else
		{
			$imageheight= 485;	
			$imagewidth = $ratio * $imageheight;
			if($imagewidth > 400)	{
				$imagewidth = 400; 
				$imageheight = $imagewidth / $ratio;
			}
		}

        $svg = '<svg id="productSvg" height="485px" width="400px" viewBox="0 0 400 485" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <filter id="colorMat" color-interpolation-filters="sRGB">
                            <feColorMatrix values="'.$feColorMatrixValue.'" type="matrix" id="feColorMatrix"></feColorMatrix>
                        </filter>        
                    </defs>
                    <image width="'.$imagewidth.'" height="'.$imageheight.'" id="img_0" xlink:href="'.$productSideImage.'" style="pointer-events:none;display:Block;" class="main_image" filter="'.$filter.'"></image>        
                </svg>';
        return $svg;
    }


    public function strPad($input, $pad_length, $pad_string, $pad_left = '') {
        // return input if length greater than padded length
        if (strlen($input) >= $pad_length)
            return $input;

        // generate padding
        $paddedString = "";
        $cnt = $pad_length - (strlen($input));
        for ($i = 0; $i < $cnt; $i++)
            $paddedString .= $pad_string;

        // concatonate results
        $resultStr = $pad_left ? ($paddedString . $input) : ($input . $paddedString);

        // account for overflow if any
        if (strlen($resultStr) > $pad_length) {
            // chop off extra from result based on pad_type
            if ($pad_left)
                $resultStr = substr($resultStr,strlen($resultStr) - $pad_length, strlen($resultStr));
            else
                $resultStr = substr($resultStr, 0, $pad_length);
        }
        return $resultStr;
    }


    /*
    Convert HEX color code to RGB color code
    */
    public function hex2rgb($hex) {

        $color = str_replace('#','',$hex);

        $rgb = array('r' => hexdec(substr($color,0,2)),
            'g' => hexdec(substr($color,2,2)),
            'b' => hexdec(substr($color,4,2)));

        return $rgb;
    }

    public function getFeColorMatrix($rgb)
    {
        $matrix = (float)$rgb['r']/255 . " 0 0 0 0 ";
        $matrix .= "0 " . (float)$rgb['g']/255 . " 0 0 0 ";
        $matrix .= "0 0 " . (float)$rgb['r']/255 . " 0 0 ";
        $matrix .= "0 0 0 1 0";
        return $matrix;
    }

}
