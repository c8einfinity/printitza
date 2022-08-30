<?php

declare(strict_types=1);

namespace Designnbuy\Template\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Order sales field resolver, used for GraphQL request processing
 */
class LayoutByCategory implements ResolverInterface
{
    protected $_templateCollectionFactory;

    public function __construct(
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
    ) {
        $this->_templateCollectionFactory = $templateCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $categoryId = $this->getCategoryId($args);
        $clipartData = $this->getClipartData($categoryId);

        return $clipartData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getCategoryId(array $args): int
    {
        if (!isset($args['category_id'])) {
            throw new GraphQlInputException(__('"Category id should be specified'));
        }

        return (int)$args['category_id'];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getClipartData($categoryId): array
    {
        try {
            $storeId = 0;

            $collection = $this->_templateCollectionFactory->create();
            //$collection = $this->_templateFactory->create()->getCollection();
            $collection->addLayoutFilter();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToSelect('type_id');
            $collection->addAttributeToSelect('status');

            $collection->joinAttribute('title','designnbuy_template/title','entity_id',null,'left',$storeId);
            
            $categoryIds[] = array('finset'=> array($categoryId));
            $collection->addAttributeToFilter('category_id', [$categoryIds]);
            //$collection->addAttributeToFilter('category_id',7);
            $layoutDt = [];
            
            if($collection){
                
                foreach($collection as $dt){
                    $layoutDt[$dt->getEntityId()]['entity_id'] = $dt->getEntityId();
                    $layoutDt[$dt->getEntityId()]['image'] = $dt->getImage();
                    $layoutDt[$dt->getEntityId()]['title'] = $dt->getTitle();
                    $layoutDt[$dt->getEntityId()]['svg'] = $dt->getMediaUrl($dt->getSvg());
                }
                //echo "<pre>"; print_r($layoutDt); exit;
                $clipartData['LayoutByCategory'] = $layoutDt;
            } else {
                throw new GraphQlNoSuchEntityException(__("Related category not found"));    
            }

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $clipartData;        
    }
}