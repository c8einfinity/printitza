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
class PhotoAlbum implements ResolverInterface
{
    protected $photsFactory;

    public function __construct(
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos\CollectionFactory $photsFactory
    ) {
        $this->photsFactory = $photsFactory;
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
        $albumId = $this->getAlbumId($args);
        $photoData = $this->getPhotosData($albumId);

        return $photoData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getAlbumId(array $args): int
    {
        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('"sales id should be specified'));
        }

        return (int)$args['id'];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getPhotosData(int $albumId): array
    {
        try {
            $photoAlbum = $this->photsFactory->create();
            $photoAlbum->addFieldToFilter('album_id',$albumId);
            $photosByAlbum['allPhotos'] = $photoAlbum->getData();
            
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        
        return $photosByAlbum;
    }
}