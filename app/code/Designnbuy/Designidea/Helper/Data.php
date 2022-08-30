<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Designidea\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Catgegory Collection
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryFactoryCollection;


    /**
     * Designidea Collection
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designIdeaFactoryCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;


    /**
     * @param \Magento\Framework\UrlInterface $frontendUrlBuilder
     */
    public function __construct(
        \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory $categoryFactoryCollection,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designIdeaFactoryCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_categoryFactoryCollection = $categoryFactoryCollection;
        $this->_designIdeaFactoryCollection = $designIdeaFactoryCollection;
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
            'file' => ['backend_model' => 'Designnbuy\Designidea\Model\Attribute\Backend\File'],
            'image' => ['backend_model' => 'Designnbuy\Designidea\Model\Attribute\Backend\Image'],
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

    public function getCategories($productId)
    {
        $collection = $this->_categoryFactoryCollection->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('status', 1)            
            //->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('title','asc');
        if(isset($productId)){
            $collection->addProductFilter($productId);
        }

        $categories = array();
        foreach($collection as $category)
        {
            $categories[$category->getEntityId()] = $category->getTitle();
        }

        return $categories;
    }

    /**
     * Retrieve clipart related cliparts
     * @return \Designnbuy\Clipart\Model\ResourceModel\Clipart\Collection
     */
    public function getCategoryRelatedDesignIdeas($categoryId = null)
    {
        $collection = $this->_designIdeaFactoryCollection->create()
            ->addAttributeToSelect('*')
            ->addActiveFilter()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->setOrder('title','asc');

        if(isset($categoryId)){
            $collection->addCategoryFilter($categoryId);
        }
        
        /*if ($this->getStoreId()) {
            $collection->addStoreFilter($this->getStoreId());
        }*/
        $designIdeas = [];
        $i = 0;
        foreach ($collection as $designIdea) {


            if($designIdea->getSvg()){
                $designIdeas[$i] = [
                    'id' => $designIdea->getId(),
                    'title' => $designIdea->getTitle(),
                    'image' => $designIdea->getImage(),
                    'svg' => '',
                ];
                $svgs = explode(',',$designIdea->getSvg());
                $svgUrls = [];
                foreach ($svgs as $svg){
                    $svgUrls[] = $designIdea->getMediaUrl($svg);
                }
                $designIdeas[$i]['svg'] = implode(',', $svgUrls);
            }
            $i++;
        }

        return $designIdeas;
    }
}
