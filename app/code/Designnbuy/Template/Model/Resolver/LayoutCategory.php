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
class LayoutCategory implements ResolverInterface
{
    protected $_categoryCollectionFactory;

    public function __construct(
        \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
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
        $clipartData = $this->getClipartData();

        return $clipartData;
    }

    /**
     * @param int $orderId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getClipartData(): array
    {
        try {
            $storeId = 0;

            $collection = $this->_categoryCollectionFactory->create();
            $collection->addAttributeToSelect('entity_id');

            $collection->joinAttribute('title','designnbuy_template_category/title','entity_id',null,'left',$storeId);
            $collection->joinAttribute('status','designnbuy_template_category/status','entity_id',null,'left',$storeId);
            $collection->addAttributeToFilter('status',1);
            if($collection){
                $clipartData['allLayout'] = $collection->getData();
            } else {
                throw new GraphQlNoSuchEntityException(__("Related category not found"));    
            }

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $clipartData;        
    }
}