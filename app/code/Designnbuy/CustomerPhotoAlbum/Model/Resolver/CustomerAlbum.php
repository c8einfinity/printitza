<?php

declare(strict_types=1);

namespace Designnbuy\CustomerPhotoAlbum\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Order sales field resolver, used for GraphQL request processing
 */
class CustomerAlbum implements ResolverInterface
{
    protected $albumFactory;

    protected $helper;

    public function __construct(
        \Designnbuy\CustomerPhotoAlbum\Model\Album $albumFactory,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $helper
    ) {
        $this->albumFactory = $albumFactory;
        $this->helper = $helper;
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
        $customerId = $this->getCustomerId($args);
        $storeId = $this->getStoreId($args);
        $albumData = $this->getAlbumData($customerId, $storeId);

        return $albumData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getCustomerId(array $args): int
    {
        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('"customer id should be specified'));
        }

        return (int)$args['id'];
    }
    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getStoreId(array $args): int
    {
        if (!isset($args['storeid'])) {
            throw new GraphQlInputException(__('"customer Store Id should be specified'));
        }

        return (int)$args['storeid'];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getAlbumData(int $customerId, int $storeId): array
    {
        try {
            $photoAlbum = $this->albumFactory->getCollection();
            $photoAlbum->addFieldToFilter('customer_id',$customerId);
            $photoAlbum->addFieldToFilter('status',1);
            $photoAlbum->addStoreData();
            
            $allAlbum = array();
            foreach($photoAlbum as $key => $singleAlbum){
                if($singleAlbum->getCustomerId() == '999999999' && $storeId){
                    if (!empty($singleAlbum->getStoreId()) && !in_array($storeId, $singleAlbum->getStoreId()))
                    {
                        continue;
                    }
                }

                $albumImage['image'] = $this->helper->getCustomerAlbumsPhotos($singleAlbum->getAlbumId());
                $allAlbum[$key] = array_merge($singleAlbum->getData(),$albumImage);
            }
            $photosByAlbum['allAlbum'] = $allAlbum;
            
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        
        return $photosByAlbum;
    }
}