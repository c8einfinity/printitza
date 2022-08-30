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
class CustomerCreateAlbum implements ResolverInterface
{
    protected $albumFactory;

    protected $helper;

    protected $_storeManager; 
    
    public function __construct(
        \Designnbuy\CustomerPhotoAlbum\Model\Album $album,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $helper
    ) {
        $this->albumFactory = $album;
        $this->_storeManager = $storeManager;
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
        $albumTitle = $this->getAlbumTitle($args);
        $customerId = $this->getCustomerId($args);
        $storeId = $this->getStoreId($args);
        $albumData['album_id'] = $this->createNewAlbum($albumTitle,$customerId,$storeId);

        return $albumData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getAlbumTitle(array $args): string
    {
        if (!isset($args['title'])) {
            throw new GraphQlInputException(__('album title should be specified'));
        }

        return (string)$args['title'];
    }
    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getCustomerId(array $args): string
    {
        if (!isset($args['customer_id'])) {
            throw new GraphQlInputException(__('customer id should be specified'));
        }

        return (string)$args['customer_id'];
    }
    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getStoreId(array $args): string
    {
        if (!isset($args['storeid'])) {
            throw new GraphQlInputException(__('Store id should be specified'));
        }

        return (string)$args['storeid'];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function createNewAlbum(string $albumTitle, string $customerId, string $storeId): string
    {
        try {
            $model = $this->albumFactory;
            $model->setTitle($albumTitle);
            $model->setCustomerId($customerId);
            if($customerId == "999999999" && $storeId != "" && $storeId != 0){
                $model->setStores($storeId);
            }
            $model->save();
            return $model->getAlbumId(); 
            
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        
        return $photosByAlbum;
    }
}