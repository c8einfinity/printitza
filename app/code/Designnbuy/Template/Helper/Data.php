<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Template\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{


    /**
     * Template Collection
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateFactoryCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var ProductOptionValueModel
     * @since 101.0.0
     */
    protected $productOptionValueModel;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;


    /**
     * @param \Magento\Framework\UrlInterface $frontendUrlBuilder
     */
    public function __construct(
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateFactoryCollection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\Option\Value $productOptionValueModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_templateFactoryCollection = $templateFactoryCollection;
        $this->productRepository = $productRepository;
        $this->productOptionValueModel = $productOptionValueModel;
        $this->_storeManager = $storeManager;
    }

    /**
     * Return information array of customer attribute input types
     *
     * @param string $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = [
            'multiselect' => ['backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                               'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table'],
            'boolean' => ['source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'],
            'file' => ['backend_model' => 'Designnbuy\Template\Model\Attribute\Backend\File'],
            'image' => ['backend_model' => 'Designnbuy\Template\Model\Attribute\Backend\Image'],
        ];

        if ($inputType === null) {
            return $inputTypes;
        } else {
            if (isset($inputTypes[$inputType])) {
                return $inputTypes[$inputType];
            }
        }
        return [];
    }
    
    /**
     * Return default attribute backend model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }
    
    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    public function getRelatedTemplates($params)
    {
        if(isset($params) && !empty($params['pid'])) {
            $productId = $params['pid'];
            $product = $this->productRepository->getById($productId);

            if (isset($params) && !empty($params['sizeOptionId']) && !empty($params['sizeOptionOptionId'])) {
                $sizeOptionId = $params['sizeOptionId'];
                $sizeOptionOptionId = $params['sizeOptionOptionId'];
                $productOptionValues = $this->productOptionValueModel
                    ->getValuesByOption(array($sizeOptionOptionId), $sizeOptionId, $this->_storeManager->getStore()->getId())
                    ->addTitleToResult($this->_storeManager->getStore()->getId());
                $result = $productOptionValues->toArray();
                $result = reset($result['items']);
                if (isset($result['designtool_title']) && $result['designtool_title'] != '') {
                    $sizeTitle = $result['designtool_title'];
                } else {
                    $sizeTitle = $result['title'];
                }
                $size = explode('x', strtolower($sizeTitle));
                $width = $size[0];
                $height = $size[1];
            } else {
                $width = $product->getWidth();
                $height = $product->getHeight();
            }
            if($product->getIsPhotobook() && isset($params['side'])){
                $width = $width / 2;
            }

            $templateCollection = $this->_templateFactoryCollection->create()
                ->addAttributeToSelect('*')
                ->addActiveFilter()
                ->addProductFilter($productId)
                ->addWebSiteFilter($this->_storeManager->getWebsite()->getId(), false)
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setOrder('e.position', 'ASC');
            $templateCollection->getSelect()->order('position', 'ASC');

            $templateCollection->addFieldToFilter('width', array("eq" => sprintf("%.4f", $width)));
            $templateCollection->addFieldToFilter('height', array("eq" => sprintf("%.4f", $height)));
            return $templateCollection;
        }
        return;

    }

    public function getRelatedTemplatesWithoutWidthHeight($params)
    {
        if(isset($params) && !empty($params['pid'])) {
            $productId = $params['pid'];
            $product = $this->productRepository->getById($productId);

            $templateCollection = $this->_templateFactoryCollection->create()
                ->addAttributeToSelect('*')
                ->addActiveFilter()
                ->addProductFilter($productId)
                ->addWebSiteFilter($this->_storeManager->getWebsite()->getId(), false)
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setOrder('e.position', 'ASC');
            $templateCollection->getSelect()->order('position', 'ASC');

            //$templateCollection->addFieldToFilter('width', array("eq" => sprintf("%.4f", $width)));
            //$templateCollection->addFieldToFilter('height', array("eq" => sprintf("%.4f", $height)));
            return $templateCollection;
        }
        return;

    }

    public function getProductRelatedTemplates($params)
    {
        $templateCollection = $this->getRelatedTemplates($params);
        $templateCollection->addTemplateFilter();

        $templates = [];
        foreach ($templateCollection as $template) {
            $templateSvgs = [];
            $svgs = explode(',', $template->getSvg());
            foreach ($svgs as $svg) {
                $templateSvgs[] = $template->getMediaUrl($svg);
            }
            $templates[] = [
                'id' => $template->getId(),
                'title' => $template->getTitle(),
                'image' => $template->getImage(),
                'svg' => implode(',', $templateSvgs)
            ];
        }
        
        return $templates;
    }

    public function getProductRelatedLayouts($params)
    {
        $templateCollection = $this->getRelatedTemplates($params);
        $templateCollection->addLayoutFilter();
        $side = $params['side'];
        if($side == 'Front'){
            $sideId = 'front';
        }elseif ($side == 'Inside'){
            $sideId = 'inside';
        }elseif ($side == 'Back'){
            $sideId = 'back';
        }

        $templateCollection->addAttributeToFilter('layout_side', array(
                'finset' => array(
                    $sideId
                )
            )
        );
        $templates = [];
        foreach ($templateCollection as $template) {
            $templates[] = [
                'id' => $template->getId(),
                'title' => $template->getTitle(),
                'image' => $template->getImage(),
                'svg' => $template->getMediaUrl($template->getSvg()),
            ];
        }
        return $templates;
    }
    public function getProductRelatedAllLayouts($params)
    {
        $templateCollection = $this->getRelatedTemplatesWithoutWidthHeight($params);
        $templateCollection->addLayoutFilter();
        return $templateCollection->getSize();
    }
    public function getProductRelatedAllTemplates($params)
    {
        $templateCollection = $this->getRelatedTemplatesWithoutWidthHeight($params);
        $templateCollection->addTemplateFilter();

        return $templateCollection->getSize();
    }
}
