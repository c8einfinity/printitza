<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\FastOrder\Block;

use Magento\Framework\View\Element\Template;

class FastOrder extends Template
{
    protected $productCollectionFactory;
    protected $productStatus;		
    protected $productVisibility;
    protected $helperBss;


    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Bss\FastOrder\Helper\Data $helperBss,		
		\Bss\FastOrder\Model\Search\Save $save,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->helperBss = $helperBss;		
        $this->_isScopePrivate = true;
		$this->save = $save;
    }

    public function getFormAction()
    {
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
            return $this->getUrl('fastorder/index/add', ['_secure' => true]);
        } else { 
            return $this->getUrl('fastorder/index/add');
        }
    }

    public function getUrlCsv()
    {
        $fileName = 'import_fastorder.csv';
        $url = $this->getViewFileUrl('Bss_FastOrder::csv/bss/fastorder/'.$fileName);
        return $url;
    }
	
	public function getFastorderProducts()
	{
		return $respon = $this->save->getFastorderProductsInfo();
	}
	
}
